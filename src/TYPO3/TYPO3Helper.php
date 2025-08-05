<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Test\Constraint\Source\ElementHasClass;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use CoStack\StackTest\Test\Expectation\ElementPositionDoesNotChange;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

use function array_shift;
use function array_values;
use function is_string;

class TYPO3Helper
{
    public static function inContentIFrameContext(WebDriver $driver, Closure $closure): void
    {
        $driver->inIFrameContext(WebDriverBy::id('typo3-contentIframe'), $closure);
    }

    public static function waitUntilContentIFrameIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::contentIFrameIsLoaded());
    }

    public static function waitUntilTreeIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::treeIsLoaded($driver));
    }

    public static function waitUntilModalIsOpen(WebDriver $driver): void
    {
        $selector = WebDriverBy::xpath('//typo3-backend-modal/div[contains(@class, "modal")]');
        $driver->wait()->until(ElementIsVisible::resolve($selector));
        $driver->wait()->until(ElementPositionDoesNotChange::build($selector));
        $driver->wait()->until(ElementHasClass::resolve('show', $selector));
    }

    public static function waitUntilModalIsClosed(WebDriver $driver): void
    {
        $selector = WebDriverBy::xpath('//typo3-backend-modal/div[contains(@class, "modal")]');
        $driver->wait()->until(ElementIsNotVisible::resolve($selector));
        $driver->wait()->until(ElementPositionDoesNotChange::build($selector));
    }

    public static function backendLogin(WebDriver $driver, string $url, string $username, string $password): void
    {
        $driver->get($url);

        $loginFormSelector = WebDriverBy::name('loginform');

        $constrain = new ElementIsVisible($driver);
        $loginFormExists = $constrain->eval($loginFormSelector);
        if ($loginFormExists) {
            $driver->submitForm($loginFormSelector, [
                'username' => $username,
                'p_field' => $password,
            ]);
        } else {
            // Login form does not exist, so either the backend is broken or we're already logged in
            $backendToolbarUserMenu = WebDriverBy::cssSelector('.toolbar-item-user .toolbar-item-name');
            $constrain = new ElementIsVisible($driver);
            $backendUserIsLoggedIn = $constrain->eval($backendToolbarUserMenu);
            if (!$backendUserIsLoggedIn) {
                throw new Exception(
                    'TYPO3 Backend seems to be broken. No user is logged in and no login form could be found',
                );
            }
            $loggedInUser = $driver->findElement($backendToolbarUserMenu)->getText();
            if (trim($loggedInUser) !== $username) {
                self::backendLogout($driver);
                self::backendLogin($driver, $url, $username, $password);
            }
        }
        self::waitUntilContentIFrameIsLoaded($driver);
    }

    public static function backendLogout(WebDriver $driver): void
    {
        $toolbarUserMenu = WebDriverBy::cssSelector('.toolbar-item.toolbar-item-user');
        $driver->click($toolbarUserMenu);
        $driver->findElement(WebDriverBy::linkText('Logout'))->click();
    }

    /**
     * Can be replaced with but is more precise as:
     * <code class="code">$session->click(WebDriverBy::linkText($text));</code>
     */
    public static function selectModuleByText(WebDriver $driver, string $text): void
    {
        $xpath = "//nav[@id='modulemenu']//span[@class='modulemenu-name' and text()='$text']/ancestor::a";
        $moduleLink = $driver->findElement(WebDriverBy::xpath($xpath));
        $moduleLink->click();
        self::waitUntilContentIFrameIsLoaded($driver);
    }

    public static function selectInPageTree(
        WebDriver $driver,
        array $pagePath,
        Closure $afterSelectionCallback = null,
    ): void {
        $pagePath = array_values($pagePath);
        $currentLevel = 1;
        $lastNode = null;

        foreach ($pagePath as $index => $page) {
            $currentLevel++;

            // First try to find the node at current level
            $nodeLocator = WebDriverBy::xpath(
                "//div[contains(@class, 'node') and @aria-level='{$currentLevel}']" .
                "//div[contains(@class, 'node-name') and normalize-space(text())='{$page}']"
            );

            try {
                // Wait for node to become visible
                $node = self::waitForElement($driver, $nodeLocator, 10);

                // If this is not the last item in the path, we need to check if the node needs expansion
                if ($index < count($pagePath) - 1) {
                    // Get the parent node element
                    $parentNode = $node->findElement(
                        WebDriverBy::xpath("ancestor::div[contains(@class, 'node')][@aria-expanded][1]")
                    );

                    // Check if node needs to be expanded
                    if ($parentNode->getAttribute('aria-expanded') === '0') {
                        // Find and click the toggle
                        $toggle = $parentNode->findElement(
                            WebDriverBy::cssSelector('span.node-toggle')
                        );
                        $toggle->click();

                        // Wait for the children to load
                        self::waitForAjax($driver);
                        usleep(500000);
                    }
                } // Only click the node if it's the last one in the path
                else {
                    $nodeContent = $node->findElement(
                        WebDriverBy::xpath("ancestor::div[contains(@class, 'node-content')][1]")
                    );
                    $nodeContent->click();
                    $lastNode = $nodeContent;
                }
            } catch (NoSuchElementException $e) {
                throw new Exception("Could not find or interact with page '{$page}' in the page tree", 1706624556, $e);
            }
        }

        self::waitUntilContentIFrameIsLoaded($driver);
        if (null !== $afterSelectionCallback && $lastNode !== null) {
            $afterSelectionCallback($driver, $lastNode);
        }
    }

    private static function waitForElement(WebDriver $driver, $locator, $timeout = 10)
    {
        return $driver->wait($timeout, 250)->until(
            WebDriverExpectedCondition::presenceOfElementLocated($locator)
        );
    }

    private static function waitForAjax(WebDriver $driver, $timeout = 10)
    {
        $driver->wait($timeout, 250)->until(
            function () use ($driver) {
                try {
                    $spinner = $driver->findElement(
                        WebDriverBy::cssSelector('.node-loader[style*="display: block"]')
                    );
                    return false;
                } catch (NoSuchElementException $e) {
                    return true;
                }
            }
        );
    }

    public static function searchInPageTreeAndSelectFirstOccurrence(
        WebDriver $driver,
        string $searchString,
    ) {
        $searchField = $driver->findElement(
            WebDriverBy::xpath('//*[@id="typo3-pagetree-toolbar"]//input[@type="search"]'),
        );
        $searchField->clear();
        $searchField->sendKeys($searchString);

        $driver->wait()->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::xpath('//div[@class="node-content"]//div[@class="node-contentlabel"]//span[@class="node-highlight-text"]')
            )
        );

        $pageTreeElement = $driver->findElement(
            WebDriverBy::xpath('//span[@class="node-highlight-text"]/ancestor::div[@role="treeitem" and contains(@class, "node")]')
        );

        $driver->action()->moveToElement($pageTreeElement)->click()->perform();
    }

    public static function reloadBackendPage(WebDriver $driver): void
    {
        $driver->navigate()->refresh();
        self::waitUntilContentIFrameIsLoaded($driver);
        self::waitUntilTreeIsLoaded($driver);
    }

    /**
     * Clear page tree search and reset state
     */
    public static function clearPageTreeSearch(WebDriver $driver): void
    {
        // Clear the search field
        $searchField = $driver->findElement(
            WebDriverBy::xpath('//*[@id="typo3-pagetree-toolbar"]//input[@type="search"]'),
        );
        $searchField->clear();

        // Clear any selections programmatically
        $driver->executeScript('
        // Remove all selected classes
        document.querySelectorAll(".node-selected").forEach(el => {
            el.classList.remove("node-selected");
            el.removeAttribute("tabindex");
        });
        
        // Remove all highlights
        document.querySelectorAll(".node-highlight-text").forEach(el => {
            const parent = el.parentNode;
            parent.replaceChild(document.createTextNode(el.textContent), el);
        });
        
        // Reset focus
        if (document.activeElement) {
            document.activeElement.blur();
        }
    ');

        sleep(1);
    }


    public static function selectInFileStorageTree(
        WebDriver $driver,
        array $filePath,
        Closure $afterSelectionCallback = null,
    ): void {
        $filePath = array_values($filePath);
        $currentLevel = 0;
        $lastNode = null;

        foreach ($filePath as $index => $file) {
            $currentLevel++;

            // Find the node by level and name
            $nodeLocator = WebDriverBy::xpath(
                "//div[contains(@class, 'navigation-tree-container')]" .
                "//div[contains(@class, 'node') and @aria-level='{$currentLevel}']" .
                "//div[contains(@class, 'node-name') and normalize-space(text())='{$file}']"
            );

            try {
                // Wait for node to become visible
                $node = self::waitForElement($driver, $nodeLocator, 10);

                // If this is not the last item, we need to handle expansion
                if ($index < count($filePath) - 1) {
                    $parentNode = $node->findElement(
                        WebDriverBy::xpath("ancestor::div[contains(@class, 'node')][@aria-expanded][1]")
                    );

                    // If not expanded (aria-expanded="0"), click the toggle
                    if ($parentNode->getAttribute('aria-expanded') === '0') {
                        $toggle = $parentNode->findElement(
                            WebDriverBy::cssSelector('span.node-toggle')
                        );
                        $toggle->click();

                        // Wait for the expansion and children to load
                        self::waitForAjax($driver);
                        usleep(500000);

                        // First verify the parent node is expanded
                        self::waitForElement(
                            $driver,
                            WebDriverBy::xpath(
                                "//div[contains(@class, 'node')][@aria-expanded='1']" .
                                "//div[contains(@class, 'node-name') and normalize-space(text())='{$file}']"
                            ),
                            5
                        );

                        // Wait for child nodes to become visible (next level)
                        $nextLevel = $currentLevel + 1;
                        self::waitForElement(
                            $driver,
                            WebDriverBy::xpath(
                                "//div[contains(@class, 'navigation-tree-container')]" .
                                "//div[contains(@class, 'node') and @aria-level='{$nextLevel}']"
                            ),
                            5
                        );
                    }
                } else {
                    // For the last node, find and click its node-content directly
                    $nodeContent = $node->findElement(
                        WebDriverBy::xpath("ancestor::div[contains(@class, 'node-content')][1]")
                    );
                    $nodeContent->click();
                    $lastNode = $nodeContent;

                    // Verify selection
                    self::waitForElement(
                        $driver,
                        WebDriverBy::xpath(
                            "//div[contains(@class, 'node-selected')]" .
                            "//div[contains(@class, 'node-name') and normalize-space(text())='{$file}']"
                        ),
                        5
                    );
                }
            } catch (NoSuchElementException $e) {
                throw new Exception(
                    "Could not find or interact with file/folder '{$file}' in the file tree at level {$currentLevel}",
                    1706624557,
                    $e
                );
            }
        }

        if (null !== $afterSelectionCallback && $lastNode !== null) {
            $afterSelectionCallback($driver, $lastNode);
        }
    }

    public static function fillTYPO3FormField(WebDriver $driver, string $label, string $value): void
    {
        // Search for label with and without whitespace, because TYPO3 adds one whitespace at the end
        $xpath = WebDriverBy::xpath(
            "//input[@type!='hidden'][ancestor::div[contains(@class, 'form-group')]/label[text()='{$label}' or text()='{$label} ']]",
        );
        $inputElement = $driver->findElement($xpath);
        $inputElement->sendKeys($value);
    }

    public static function refreshPageTree(WebDriver $driver): void
    {
        $driver->executeScript('top.document.dispatchEvent(new CustomEvent("typo3:pagetree:refresh"));');
        self::waitUntilTreeIsLoaded($driver);
    }

    public static function refreshFileStorageTree(WebDriver $driver): void
    {
        $driver->executeScript('top.document.dispatchEvent(new CustomEvent("typo3:filestoragetree:refresh"));');
        self::waitUntilTreeIsLoaded($driver);
    }

    /**
     * Must not be called in IFrame Context!
     */
    public static function clickModalButton(WebDriver $driver, string|WebDriverBy $buttonTextOrSelector): void
    {
        if ($driver->isInIFrameContext()) {
            throw new Exception(__METHOD__ . ' must not be called in IFrame context');
        }
        if (is_string($buttonTextOrSelector)) {
            $buttonTextOrSelector = WebDriverBy::xpath("//button[text()='$buttonTextOrSelector']");
        }
        self::waitUntilModalIsOpen($driver);
        $modal = $driver->findElement(WebDriverBy::xpath('//typo3-backend-modal/div[contains(@class, "modal")]'));
        $button = $modal->findElement($buttonTextOrSelector);
        $button->click();
        self::waitUntilModalIsClosed($driver);
    }

    public static function clickContentElementFromNewContentElementWizard(
        WebDriver $driver,
        string $contentElementSelector
    ): void {
        if ($driver->isInIFrameContext()) {
            throw new Exception(__METHOD__ . ' must not be called in IFrame context');
        }
        self::waitUntilModalIsOpen($driver);
        sleep(2);
        $wizardElement = $driver->findElement(WebDriverBy::tagName('typo3-backend-new-record-wizard'));
        $contentElementButton = $wizardElement->getShadowRoot()
                                              ->findElement(WebDriverBy::cssSelector('button[data-identifier="' . $contentElementSelector . '"]'));
        $contentElementButton->click();

        self::waitUntilModalIsClosed($driver);
    }

}
