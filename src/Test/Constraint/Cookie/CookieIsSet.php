<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchCookieException;

class CookieIsSet extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        if ($other instanceof Cookie) {
            $other = $other->getName();
        }
        try {
            $this->driver->manage()->getCookieNamed($other);
        } catch (NoSuchCookieException $e) {
            return false;
        }
        return true;
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is set on page %s in browser %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
