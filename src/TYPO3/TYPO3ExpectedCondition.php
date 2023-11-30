<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Session\Session;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class TYPO3ExpectedCondition extends WebDriverExpectedCondition
{
    public static function contentIFrameIsLoaded(): Closure
    {
        return static function (Session|RemoteWebDriver $driver): bool {
            return ElementIsVisible::resolve(WebDriverBy::id('typo3-contentIframe'))($driver)
                && ElementIsNotVisible::resolve(WebDriverBy::id('nprogress'))($driver);
        };
    }

    public static function pageTreeIsLoaded(): Closure
    {
        $selector = WebDriverBy::className('svg-tree-loader');
        $svgTreeLoaderExists = ElementExists::resolve($selector);
        $svgTreeLoaderIsNotVisible = ElementIsNotVisible::resolve($selector);

        return static function (Session|RemoteWebDriver $session) use ($svgTreeLoaderExists, $svgTreeLoaderIsNotVisible): bool {
            return $svgTreeLoaderExists($session)
                && $svgTreeLoaderIsNotVisible($session);
        };
    }
}
