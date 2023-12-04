<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\WebDriverBy;

class PageContains extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $selector = WebDriverBy::cssSelector('body');
        $remoteWebElement = $driver->findElement($selector);
        $text = $remoteWebElement->getText();
        return str_contains($text, $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is displayed on page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
