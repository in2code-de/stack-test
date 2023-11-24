<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use CoStack\StackTest\Session;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\Constraint\Constraint;

abstract class SessionConstrain extends Constraint
{
    protected array $driverResults = [];

    public function __construct(
        protected readonly Session|RemoteWebDriver $session,
    ) {
    }

    protected function matches(mixed $other): bool
    {
        if ($this->session instanceof RemoteWebDriver) {
            $browserName = $this->session->getCapabilities()->getBrowserName();
            $this->driverResults[$browserName] = $this->driverMatches($other, $this->session);
            return $this->driverResults[$browserName];
        }

        foreach ($this->session->getDrivers() as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $this->driverResults[$browserName] = $this->driverMatches($other, $driver);
        }

        return !in_array(false, $this->driverResults, true);
    }

    abstract protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool;

    public function toString(bool $exportObjects = false): string
    {
        if ($this->session instanceof RemoteWebDriver) {
            return $this->descriptionForDriver($this->session, $exportObjects);
        }
        $frame = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $onlyFailedDrivers = 'failureDescription' === $frame['function'];

        $strings = [];
        foreach ($this->session->getDrivers() as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            if (!$onlyFailedDrivers || false === $this->driverResults[$browserName]) {
                $strings[] = $this->descriptionForDriver($driver, $exportObjects);
            }
        }
        return implode(' and ', $strings);
    }

    abstract protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string;
}
