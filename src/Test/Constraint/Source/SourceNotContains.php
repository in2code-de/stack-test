<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Source;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class SourceNotContains extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $source = $driver->getPageSource();
        return !str_contains($source, $other);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not contained in the source of page %s in %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
