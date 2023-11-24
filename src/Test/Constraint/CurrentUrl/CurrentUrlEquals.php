<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\CurrentUrl;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class CurrentUrlEquals extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $currentUrl = $driver->getCurrentURL();
        return $currentUrl === $other;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'equals %s in %s',
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
