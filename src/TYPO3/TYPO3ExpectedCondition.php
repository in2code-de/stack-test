<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use CoStack\StackTest\Test\Constraint\Existence\ElementNotExists;
use CoStack\StackTest\Test\Constraint\Script\ScriptReturnSame;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class TYPO3ExpectedCondition extends WebDriverExpectedCondition
{
    public static function contentIFrameIsLoaded(): Closure
    {
        return static function (RemoteWebDriver $driver): bool {
            return ElementIsVisible::resolve(WebDriverBy::cssSelector('#typo3-contentIframe'))($driver)
                && ElementNotExists::resolve(WebDriverBy::cssSelector('#nprogress'))($driver)
                && ScriptReturnSame::resolve(true, 'return document.readyState === "complete"')($driver)
                && (function (RemoteWebDriver $driver): bool {
                    $return = false;
                    $driver->inIFrameContext(WebDriverBy::id('typo3-contentIframe'), static function (RemoteWebDriver $driver) use (&$return): void {
                        $return = ScriptReturnSame::resolve(true, 'return document.readyState === "complete"')($driver);
                    });
                    return $return;
                })($driver);
        };
    }

    public static function treeIsLoaded(): Closure
    {
        return static function (RemoteWebDriver $driver): bool {
            try {
                return ElementExists::resolve(
                    WebDriverBy::className('scaffold-content-navigation-available')
                )($driver);
            } catch (\Throwable $e) {
                return false;
            }
        };
    }
}
