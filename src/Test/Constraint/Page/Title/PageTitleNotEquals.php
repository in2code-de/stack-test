<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Page\Title;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class PageTitleNotEquals extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        return $this->driver->getTitle() !== $other;
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not equal to the page title %s on page %s in browser %s',
            $this->driver->getTitle(),
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
