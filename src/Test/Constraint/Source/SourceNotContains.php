<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Source;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;

class SourceNotContains extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $source = $driver->getPageSource();
        return !str_contains($source, $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not contained in the source of page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
