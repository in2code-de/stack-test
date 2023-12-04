<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsVisibleInElement extends DriverWithSelectorConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $elements = $driver->findElements($this->selector);
        foreach ($elements as $element) {
            try {
                $childElement = $element->findElement($other);
                if ($childElement->isDisplayed()) {
                    return true;
                }
            } catch (NoSuchElementException|StaleElementReferenceException) {
            }
        }
        return false;
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
