<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Visibility;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;

class ElementIsVisibleInElement extends DriverWithSelectorConstraint
{
    protected function matches(mixed $other): bool
    {
        $elements = $this->driver->findElements($this->selector);
        if (empty($elements)) {
            return false;
        }
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

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is visible on page %s in browser %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
