<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Test\Constraint\Source\ElementHasClass;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisibleInElement;
use CoStack\StackTest\Test\Expectation\ElementPositionDoesNotChange;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverBy;

use function array_pop;
use function is_string;

class TYPO3Helper
{
    public static function inContentIFrameContext(WebDriver $driver, Closure $closure): void
    {
        self::waitUntilContentIFrameIsLoaded($driver);
        $driver->inIFrameContext(WebDriverBy::id('typo3-contentIframe'), $closure);
    }

    public static function waitUntilContentIFrameIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::contentIFrameIsLoaded());
    }

    public static function waitUntilNavigationComponentIsLoaded(WebDriver $driver): void
    {
        $driver->wait()->until(TYPO3ExpectedCondition::pageTreeIsLoaded());
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
        }
        self::waitUntilContentIFrameIsLoaded($driver);
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
        self::selectModuleByText($driver, 'Page');
        self::waitUntilNavigationComponentIsLoaded($driver);
        $pageTreeContainer = $driver->findElement(WebDriverBy::cssSelector('#typo3-pagetree-treeContainer'));

        // Fail if nodes are not visible
        $constraint = new ElementIsVisible($driver);
        $constraint->evaluate(WebDriverBy::cssSelector('g.nodes > .node'));

        $pageToSelect = array_pop($pagePath);
        $pageTreeElement = $pageTreeContainer;
        foreach ($pagePath as $path) {
            $constraint->evaluate(WebDriverBy::xpath("//*[text()='$path']"));

            $pageTreeElement = $pageTreeElement->findElement(WebDriverBy::xpath("//*[text()='$path']/.."));

            try {
                // Expand the page tree if required
                $chevronElement = $pageTreeElement->findElement(WebDriverBy::cssSelector('.chevron.collapsed'));
                $chevronElement->click();
                self::waitUntilNavigationComponentIsLoaded($driver);
            } catch (NoSuchElementException) {
            }
        }
        $constraint->evaluate(WebDriverBy::xpath("//*[text()='$pageToSelect']"));
        $pageElement = $pageTreeElement->findElement(WebDriverBy::xpath("//*[text()='$pageToSelect']/.."));
        $pageElement->findElement(WebDriverBy::cssSelector('text.node-name'))->click();
        if (null !== $afterSelectionCallback) {
            $afterSelectionCallback($driver, $pageElement);
        }
        self::waitUntilContentIFrameIsLoaded($driver);
    }

    public static function selectInFileStorageTree(
        WebDriver $driver,
        array $folderPath,
        Closure $afterSelectionCallback = null,
    ): void {
        self::waitUntilNavigationComponentIsLoaded($driver);
        $fileStorageTree = $driver->findElement(WebDriverBy::id('typo3-filestoragetree-tree'));

        // Fail if nodes are not visible
        $constraint = new ElementIsVisible($driver);
        $constraint->evaluate(WebDriverBy::cssSelector('g.nodes > .node'));

        $folderToSelect = array_pop($folderPath);
        $folderTreeElement = $fileStorageTree;
        foreach ($folderPath as $path) {
            $constraint->evaluate(WebDriverBy::xpath("//*[text()='$path']/.."));

            $folderTreeElement = $folderTreeElement->findElement(WebDriverBy::xpath("//*[text()='$path']/.."));

            try {
                // Expand the page tree if required
                $chevronElement = $folderTreeElement->findElement(WebDriverBy::cssSelector('.chevron.collapsed'));
                $chevronElement->click();
                self::waitUntilNavigationComponentIsLoaded($driver);
            } catch (NoSuchElementException) {
            }
        }
        $constraint->evaluate(WebDriverBy::xpath("//*[text()='$folderToSelect']/.."));
        $folderElement = $folderTreeElement->findElement(WebDriverBy::xpath("//*[text()='$folderToSelect']/.."));
        $folderElement->findElement(WebDriverBy::cssSelector('text.node-name'))->click();
        if (null !== $afterSelectionCallback) {
            $afterSelectionCallback($driver, $folderElement);
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
        self::waitUntilNavigationComponentIsLoaded($driver);
    }

    public static function refreshFileStorageTree(WebDriver $driver): void
    {
        $driver->executeScript('top.document.dispatchEvent(new CustomEvent("typo3:filestoragetree:refresh"));');
        self::waitUntilNavigationComponentIsLoaded($driver);
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
        $modal = $driver->findElement(WebDriverBy::cssSelector('typo3-backend-modal'));
        $button = $modal->findElement($buttonTextOrSelector);
        $button->click();
        self::waitUntilModalIsClosed($driver);
    }
}
