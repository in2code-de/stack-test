<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Page\Title;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class PageTitleNotContains extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        return !str_contains($this->driver->getTitle(), $other);
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not in the page title %s on page %s in browser %s',
            $this->driver->getTitle(),
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
