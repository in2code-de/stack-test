<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\CurrentUrl;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class CurrentUrlEquals extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $currentUrl = $this->driver->getCurrentURL();
        return $currentUrl === $other;
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        return sprintf(
            'equals %s in %s',
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
