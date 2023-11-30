<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Session\Session;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class TYPO3Helper
{
    public static function inContentIFrameContext(Session $session, Closure $closure): void
    {
        self::waitUntilContentIFrameIsLoaded($session);
        $session->inIFrameContext(WebDriverBy::id('typo3-contentIframe'), $closure);
    }

    public static function waitUntilContentIFrameIsLoaded(Session|RemoteWebDriver $session): void
    {
        $session = Session::elevate($session);
        $session->waitUntil(TYPO3ExpectedCondition::contentIFrameIsLoaded());
    }

    public static function waitUntilPageTreeIsLoaded(Session|RemoteWebDriver $session): void
    {
        $session = Session::elevate($session);
        $session->waitUntil(TYPO3ExpectedCondition::pageTreeIsLoaded());
    }

    public static function backendLogin(Session $session, string $url, string $username, string $password): void
    {
        $session->get($url);

        $loginFormSelector = WebDriverBy::name('loginform');

        $constrain = new ElementIsVisible($session);
        $loginFormExists = $constrain->eval($loginFormSelector);
        if ($loginFormExists) {
            $session->submitForm($loginFormSelector, [
                'username' => $username,
                'p_field' => $password,
            ]);
        }
        self::waitUntilContentIFrameIsLoaded($session);
    }

    /**
     * Can be replaced with but is more precise as:
     * <code class="code">$session->click(WebDriverBy::linkText($text));</code>
     */
    public static function selectModuleByText(Session $session, string $text): void
    {
        $xpath = "//nav[@id='modulemenu']//span[@class='modulemenu-name' and text()='$text']/ancestor::a";
        $moduleLink = $session->findElement(WebDriverBy::xpath($xpath));
        $moduleLink->click();
        self::waitUntilContentIFrameIsLoaded($session);
    }

    public static function selectInPageTree(Session $session, array $pagePath): void
    {
        self::selectModuleByText($session, 'Page');
        self::waitUntilPageTreeIsLoaded($session);
        $session->inEachBrowser(static function (Session $session) use ($pagePath): void {
            $pageTreeContainer = $session->findElement(WebDriverBy::cssSelector('#typo3-pagetree-treeContainer'));

            // Fail if nodes are not visible
            $constraint = new ElementIsVisible($session);
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
                    self::waitUntilPageTreeIsLoaded($session);
                } catch (NoSuchElementException) {
                }
            }
            $constraint->evaluate(WebDriverBy::xpath("//*[text()='$pageToSelect']"));
            $pageElement = $pageTreeElement->findElement(WebDriverBy::xpath("//*[text()='$pageToSelect']/.."));
            $pageElement->findElement(WebDriverBy::cssSelector('text.node-name'))->click();
        });
        self::waitUntilContentIFrameIsLoaded($session);
    }

    public static function fillTYPO3FormField(Session|RemoteWebDriver $session, string $label, string $value): void
    {
        $session = Session::elevate($session);
        $session->inEachBrowser(static function (Session $session) use ($label, $value): void {
            // Search for label with and without whitespace, because TYPO3 adds one whitespace at the end
            $xpath = WebDriverBy::xpath(
                "//input[@type!='hidden'][ancestor::div[contains(@class, 'form-group')]/label[text()='{$label}' or text()='{$label} ']]",
            );
            $inputElement = $session->findElement($xpath);
            $inputElement->sendKeys($value);
        });
    }

    public static function refreshPageTree(Session|RemoteWebDriver $session): void
    {
        $session->executeScript('top.document.dispatchEvent(new CustomEvent("typo3:pagetree:refresh"));');
        self::waitUntilPageTreeIsLoaded($session);
    }
}
