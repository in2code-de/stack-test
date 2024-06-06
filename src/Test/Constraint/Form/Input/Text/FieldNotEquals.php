<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Text;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use PHPUnit\Util\Exporter;

class FieldNotEquals extends DriverWithSelectorConstraint
{
    protected function matches(mixed $other): bool
    {
        $element = $this->driver->findElement($this->selector);
        return $element->getAttribute('value') !== $other;
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        return sprintf(
            'equals not field %s value on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
