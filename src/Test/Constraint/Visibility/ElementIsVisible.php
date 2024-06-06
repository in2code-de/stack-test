<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsVisible extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $elements = $this->driver->findElements($other);
        if (empty($elements)) {
            return false;
        }
        foreach ($elements as $element) {
            try {
                if (!$element->isDisplayed()) {
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
            'is visible on page %s in browser %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
