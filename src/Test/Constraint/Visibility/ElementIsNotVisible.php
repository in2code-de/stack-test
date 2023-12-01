<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class ElementIsNotVisible extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        try {
            $element = $driver->findElement($other);
            $result = !$element->isDisplayed();
            return $result;
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return true;
        }
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not visible on page %s in browser %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
