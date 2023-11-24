<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\CurrentUrl;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class CurrentUrlNotContains extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $currentUrl = $driver->getCurrentURL();
        return !str_contains($currentUrl, $other);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is not contained in the URL of Page %s in %s',
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
