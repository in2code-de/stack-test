<?php

/**
 * @noinspection PhpUnnecessaryLocalVariableInspection
 * @noinspection OneTimeUseVariablesInspection
 */

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use Closure;
use CoStack\StackTest\Session\Session;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use PHPUnit\Framework\Constraint\Constraint;

use function debug_backtrace;
use function implode;
use function in_array;
use function is_array;

use const DEBUG_BACKTRACE_IGNORE_ARGS;

abstract class SessionConstrain extends Constraint
{
    protected array $driverResults = [];

    public function __construct(
        public readonly Session|RemoteWebDriver $session,
    ) {
    }

    public static function resolve(mixed $other): Closure
    {
        return static function (Session|RemoteWebDriver $session) use ($other): bool {
            $constraint = new static($session);
            $result = $constraint->eval($other);
            return $result;
        };
    }

    public function eval(mixed $other): bool
    {
        $result = $this->evaluate($other, returnResult: true);
        return $result;
    }

    protected function resolveSelectorsInOtherToText(WebDriver $driver, mixed $other): mixed
    {
        if ($other instanceof WebDriverBy) {
            $element = $driver->findElement($other);
            $other = $element->getText();
        }
        if (is_array($other)) {
            foreach ($other as $index => $value) {
                $other[$index] = $this->resolveSelectorsInOtherToText($driver, $value);
            }
        }
        return $other;
    }

    protected function resolveSelectorsInOtherToValue(WebDriver $driver, mixed $other): mixed
    {
        if ($other instanceof WebDriverBy) {
            $element = $driver->findElement($other);
            $other = $element->getAttribute('value');
        }
        if (is_array($other)) {
            foreach ($other as $index => $value) {
                $other[$index] = $this->resolveSelectorsInOtherToValue($driver, $value);
            }
        }
        return $other;
    }

    protected function resolveElementText(WebDriverElement $element): string
    {
        return match ($element->getTagName()) {
            'textarea', 'input' => $element->getAttribute('value'),
            default => $element->getText(),
        };
    }

    protected function matches(mixed $other): bool
    {
        if ($this->session instanceof RemoteWebDriver) {
            $browserName = $this->session->getCapabilities()->getBrowserName();
            $this->driverResults[$browserName] = $this->driverMatches($other, $this->session);
            return $this->driverResults[$browserName];
        }

        foreach ($this->session->drivers as $browserName => $driver) {
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
        foreach ($this->session->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            if (!$onlyFailedDrivers || false === $this->driverResults[$browserName]) {
                $strings[] = $this->descriptionForDriver($driver, $exportObjects);
            }
        }
        return implode(' and ', $strings);
    }

    abstract protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string;
}
