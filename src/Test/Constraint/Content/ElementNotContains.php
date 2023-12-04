<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Util\Exporter;

use function CoStack\StackTest\resolveElementText;

class ElementNotContains extends DriverWithSelectorConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $element = $driver->findElement($this->selector);
        $value = resolveElementText($element);
        return !str_contains($value, $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $element = $driver->findElement($this->selector);
        $value = resolveElementText($element);

        return sprintf(
            'occurs not in the text/value "%s" of element %s on page %s in browser %s',
            $value,
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
