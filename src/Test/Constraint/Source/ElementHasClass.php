<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Source;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Util\Exporter;

use function explode;
use function in_array;
use function sprintf;

class ElementHasClass extends DriverWithSelectorConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $elements = $driver->findElements($this->selector);
        if (empty($elements)) {
            return false;
        }
        foreach ($elements as $element) {
            $classes = explode(' ', $element->getAttribute('class') ?? '');
            if (!in_array($other, $classes)) {
                return false;
            }
        }
        return true;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is in classes of element %s on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $driver->browserName,
        );
    }
}
