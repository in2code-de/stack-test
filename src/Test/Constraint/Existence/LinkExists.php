<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Existence;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class LinkExists extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        try {
            $driver->findElement(WebDriverBy::partialLinkText($other));
        } catch (NoSuchElementException|UnexpectedResponseException) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is a link on page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
