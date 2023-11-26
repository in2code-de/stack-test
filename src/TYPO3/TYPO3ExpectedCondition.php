<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

class TYPO3ExpectedCondition extends WebDriverExpectedCondition
{
    public static function contentIFrameIsLoaded(): static
    {
        return new static(static function (WebDriver $driver): bool {
            $constraint = new ElementExists($driver);
            return $constraint->eval(WebDriverBy::id('typo3-contentIframe'))
                && !$constraint->eval(WebDriverBy::id('nprogress'));
        });
    }
}
