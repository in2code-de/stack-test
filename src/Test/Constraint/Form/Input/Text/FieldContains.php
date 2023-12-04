<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Text;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Util\Exporter;

class FieldContains extends DriverWithSelectorConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $element = $driver->findElement($this->selector);
        $value = $element->getAttribute('value');
        return str_contains($value, $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is in field %s value on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
