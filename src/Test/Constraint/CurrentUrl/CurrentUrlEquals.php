<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\CurrentUrl;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;

class CurrentUrlEquals extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $currentUrl = $driver->getCurrentURL();
        return $currentUrl === $other;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'equals %s in %s',
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
