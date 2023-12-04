<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;

class CookieIsSet extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        if ($other instanceof Cookie) {
            $other = $other->getName();
        }
        try {
            $driver->manage()->getCookieNamed($other);
        } catch (NoSuchCookieException $e) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        return sprintf(
            'is set on page %s in browser %s',
            $driver->getCurrentURL(),
            $driver->getCapabilities()->getBrowserName(),
        );
    }
}
