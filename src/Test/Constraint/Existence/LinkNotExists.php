<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Existence;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\WebDriverBy;

class LinkNotExists extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        try {
            $driver->findElement(WebDriverBy::partialLinkText($other));
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return true;
        }
        return false;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not a link on page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
