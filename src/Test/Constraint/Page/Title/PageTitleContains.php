<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Page\Title;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;

class PageTitleContains extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        return str_contains($driver->getTitle(), $other);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is in the page title %s on page %s in browser %s',
            $driver->getTitle(),
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
