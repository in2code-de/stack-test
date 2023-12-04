<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;

class CookieIsNotSet extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        if ($other instanceof Cookie) {
            $other = $other->getName();
        }
        try {
            $driver->manage()->getCookieNamed($other);
        } catch (NoSuchCookieException $e) {
            return true;
        }
        return false;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is not set on page %s in browser %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
