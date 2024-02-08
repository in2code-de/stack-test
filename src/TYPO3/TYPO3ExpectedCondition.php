<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use CoStack\StackTest\Test\Constraint\Existence\ElementNotExists;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class TYPO3ExpectedCondition extends WebDriverExpectedCondition
{
    public static function contentIFrameIsLoaded(): Closure
    {
        return static function (RemoteWebDriver $driver): bool {
            return ElementIsVisible::resolve(WebDriverBy::cssSelector('#typo3-contentIframe'))($driver)
                && ElementNotExists::resolve(WebDriverBy::cssSelector('#nprogress'))($driver);
        };
    }

    public static function pageTreeIsLoaded(): Closure
    {
        $selector = WebDriverBy::className('svg-tree-loader');
        $svgTreeLoaderExists = ElementExists::resolve($selector);
        $svgTreeLoaderIsNotVisible = ElementIsNotVisible::resolve($selector);

        return static function (RemoteWebDriver $session) use ($svgTreeLoaderExists, $svgTreeLoaderIsNotVisible): bool {
            return $svgTreeLoaderExists($session)
                && $svgTreeLoaderIsNotVisible($session);
        };
    }
}
