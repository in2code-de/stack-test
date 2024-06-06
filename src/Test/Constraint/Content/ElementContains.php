<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use PHPUnit\Util\Exporter;

use function CoStack\StackTest\resolveElementText;

class ElementContains extends DriverWithSelectorConstraint
{
    protected string $resolvedText;

    protected function matches(mixed $other): bool
    {
        $element = $this->getFirstVisibleElement($this->selector);
        $value = resolveElementText($element);
        $this->resolvedText = $value;
        return str_contains($value, $other);
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        return sprintf(
            'occurs in the text/value "%s" of element %s on page %s in browser %s',
            $this->resolvedText,
            Exporter::export($this->selector, $exportObjects),
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
