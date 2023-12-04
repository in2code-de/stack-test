<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsVisible extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        try {
            $element = $driver->findElement($other);
            $result = $element->isDisplayed();
            return $result;
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return false;
        }
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is visible on page %s in browser %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
