<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Existence;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementExists extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        try {
            $driver->findElement($other);
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'exists on page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
