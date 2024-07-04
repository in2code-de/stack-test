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

use function array_key_last;
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

    public static function waitUntilPageTreeIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::pageTreeIsLoaded());
    }

    public static function waitUntilFolderTreeIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::folderTreeIsLoaded());
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
        self::waitUntilPageTreeIsLoaded($driver);

        // Fail if nodes are not visible
        $initialNode = $driver->findElement(
            WebDriverBy::xpath('//*[@id="typo3-pagetree-treeContainer"]//*[@class="node"]'),
        );

        $pageTreeElement = $initialNode;

        $lastIndex = array_key_last($pagePath);
        foreach ($pagePath as $index => $page) {
            $webDriverBy = WebDriverBy::xpath("//following-sibling::*//*[text()='$page']/..");
            $pageTreeElement = $pageTreeElement->findElement($webDriverBy);

            if ($index !== $lastIndex) {
                try {
                    // Expand the page tree if required
                    $chevronElement = $pageTreeElement->findElement(WebDriverBy::cssSelector('.chevron.collapsed'));
                    if ($chevronElement->isDisplayed()) {
                        $chevronElement->click();
                    }
                    self::waitUntilPageTreeIsLoaded($driver);
                } catch (NoSuchElementException) {
                }
            }
        }
        $pageTreeElement->findElement(WebDriverBy::cssSelector('text.node-name'))->click();
        self::waitUntilContentIFrameIsLoaded($driver);
        if (null !== $afterSelectionCallback) {
            $afterSelectionCallback($driver, $pageTreeElement);
        }
    }

    public static function searchInPageTreeAndSelectFirstOccurrence(
        WebDriver $driver,
        string $searchString,
    )
    {
        $searchField = $driver->findElement(
            WebDriverBy::xpath('//*[@id="typo3-pagetree-toolbar"]//input[@type="search"]'),
        );
        $searchField->sendKeys($searchString);
        self::waitUntilPageTreeIsLoaded($driver);

        // Workaround
        sleep(1);

        $pageTreeElement = $driver->findElement(
            WebDriverBy::xpath('//*[@id="typo3-pagetree-treeContainer"]//*[@class="node-highlight-text"]'),
        );
        $pageTreeElement->click();
    }

    public static function selectInFileStorageTree(
        WebDriver $driver,
        array $folderPath,
        Closure $afterSelectionCallback = null,
    ): void {
        self::waitUntilFolderTreeIsLoaded($driver);

        // Fail if nodes are not visible

        $storage = array_shift($folderPath);
        $storageSelector = WebDriverBy::xpath(
            "//*[@id='typo3-filestoragetree-tree']//*[@class='node']//*[text()='$storage']/..",
        );
        $folderTreeElement = $driver->findElement($storageSelector);
        foreach ($folderPath as $path) {
            $folderSelector = WebDriverBy::xpath("./following-sibling::*//*[text()='$path']/..");
            $folderTreeElement = $folderTreeElement->findElement($folderSelector);

            try {
                // Expand the page tree if required
                $chevronElement = $folderTreeElement->findElement(WebDriverBy::cssSelector('.chevron.collapsed'));
                if ($chevronElement->isDisplayed()) {
                    $chevronElement->click();
                }
                self::waitUntilFolderTreeIsLoaded($driver);
            } catch (NoSuchElementException) {
            }
        }
        $folderTreeElement->findElement(WebDriverBy::cssSelector('text.node-name'))->click();
        if (null !== $afterSelectionCallback) {
            $afterSelectionCallback($driver, $folderTreeElement);
        }
        self::waitUntilContentIFrameIsLoaded($driver);
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
        self::waitUntilPageTreeIsLoaded($driver);
    }

    public static function refreshFileStorageTree(WebDriver $driver): void
    {
        $driver->executeScript('top.document.dispatchEvent(new CustomEvent("typo3:filestoragetree:refresh"));');
        self::waitUntilFolderTreeIsLoaded($driver);
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
}
