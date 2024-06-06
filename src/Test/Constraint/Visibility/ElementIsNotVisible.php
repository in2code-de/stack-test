<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsNotVisible extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $elements = $this->driver->findElements($other);
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

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not visible on page %s in browser %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
