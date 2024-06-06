<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use PHPUnit\Util\Exporter;

use function CoStack\StackTest\resolveElementText;

class ElementNotEquals extends DriverWithSelectorConstraint
{
    protected function matches(mixed $other): bool
    {
        $element = $this->driver->findElement($this->selector);
        $value = resolveElementText($element);
        return $value !== $other;
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        $element = $this->driver->findElement($this->selector);
        $value = resolveElementText($element);

        return sprintf(
            'is not the same as value "%s" of element %s on page %s in browser %s',
            $value,
            Exporter::export($this->selector, $exportObjects),
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
