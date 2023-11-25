<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\SessionWithSelectorConstraint;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Util\Exporter;

class ElementContains extends SessionWithSelectorConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $element = $driver->findElement($this->selector);
        $value = $this->resolveElementText($element);
        return str_contains($value, $other);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $element = $driver->findElement($this->selector);
        $value = $this->resolveElementText($element);

        return sprintf(
            'occurs in the text/value "%s" of element %s on page %s in browser %s',
            $value,
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
