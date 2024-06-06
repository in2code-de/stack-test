<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Existence;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\WebDriverBy;

class LinkNotExists extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        try {
            $this->driver->findElement(WebDriverBy::partialLinkText($other));
        } catch (NoSuchElementException|StaleElementReferenceException) {
            return true;
        }
        return false;
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not a link on page %s in %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
