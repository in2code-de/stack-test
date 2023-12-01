<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Existence;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class ElementExists extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        try {
            $driver->findElement($other);
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'exists on page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
