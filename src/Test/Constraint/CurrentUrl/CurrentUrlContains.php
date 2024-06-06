<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\CurrentUrl;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class CurrentUrlContains extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $currentUrl = $this->driver->getCurrentURL();
        return str_contains($currentUrl, $other);
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is contained in the URL of Page %s in %s',
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
