<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Page\Title;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class PageTitleNotContains extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        return !str_contains($driver->getTitle(), $other);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not in the page title %s on page %s in browser %s',
            $driver->getTitle(),
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
