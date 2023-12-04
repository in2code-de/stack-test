<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsNotVisible extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $elements = $driver->findElements($other);
        foreach ($elements as $element) {
            try {
                if ($element->isDisplayed()) {
                    return false;
                }
            } catch (NoSuchElementException|StaleElementReferenceException) {
            }
        }
        return true;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not visible on page %s in browser %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
