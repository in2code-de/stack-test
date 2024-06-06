<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Source;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class SourceNotContains extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $source = $this->driver->getPageSource();
        return !str_contains($source, $other);
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not contained in the source of page %s in %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
