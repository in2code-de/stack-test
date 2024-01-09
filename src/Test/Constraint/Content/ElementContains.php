<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Util\Exporter;

use function CoStack\StackTest\resolveElementText;

class ElementContains extends DriverWithSelectorConstraint
{
    protected string $resolvedText;

    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $element = $this->getFirstVisibleElement($driver, $this->selector);
        $value = resolveElementText($element);
        $this->resolvedText = $value;
        return str_contains($value, $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'occurs in the text/value "%s" of element %s on page %s in browser %s',
            $this->resolvedText,
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
