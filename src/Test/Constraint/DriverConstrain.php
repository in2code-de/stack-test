<?php

/**
 * @noinspection PhpUnnecessaryLocalVariableInspection
 * @noinspection OneTimeUseVariablesInspection
 */

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use Closure;
use CoStack\StackTest\WebDriver\Remote\MultiWebDriver;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\Constraint\Constraint;

use function CoStack\StackTest\resolve;
use function debug_backtrace;
use function implode;
use function in_array;
use function is_array;

use function reset;

use const DEBUG_BACKTRACE_IGNORE_ARGS;

abstract class DriverConstrain extends Constraint
{
    protected array $driverResults = [];

    public function __construct(
        public readonly WebDriver $driver,
    ) {
    }

    public static function resolve(mixed $other): Closure
    {
        return static function (WebDriver $driver) use ($other): bool {
            $constraint = new static($driver);
            $result = $constraint->eval($other);
            return $result;
        };
    }

    public function eval(mixed $other): bool
    {
        return $this->evaluate($other, returnResult: true);
    }

    protected function resolveSelectorsInOtherToText(WebDriver $driver, mixed $other): mixed
    {
        if ($other instanceof WebDriverBy) {
            $element = $driver->findElement($other);
            $other = $element->getText();
        }
        if (is_array($other)) {
            foreach ($other as $index => $value) {
                $other[$index] = resolve($driver, $value);
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
                $other[$index] = resolve($driver, $value);
            }
        }
        return $other;
    }

    protected function matches(mixed $other): bool
    {
        $drivers = $this->driver instanceof MultiWebDriver ? $this->driver->drivers : [$this->driver];
        foreach ($drivers as $driver) {
            $this->driverResults[$driver->browserName] = $this->driverMatches($other, $driver);
        }

        return !in_array(false, $this->driverResults, true);
    }

    abstract protected function driverMatches(mixed $other, WebDriver $driver): bool;

    public function toString(bool $exportObjects = false): string
    {
        if ($this->driver instanceof MultiWebDriver) {
            $frame = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
            $onlyFailedDrivers = 'failureDescription' === $frame['function'];

            $strings = [];
            foreach ($this->driver->drivers as $driver) {
                $browserName = $driver->getCapabilities()->getBrowserName();
                if (!$onlyFailedDrivers || false === $this->driverResults[$browserName]) {
                    $strings[] = $this->descriptionForDriver($driver, $exportObjects);
                }
            }
            return implode(' and ', $strings);
        }
        return $this->descriptionForDriver($this->driver, $exportObjects);
    }

    abstract protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string;

    protected function getFirstVisibleElement(WebDriver $driver, WebDriverBy $selector): RemoteWebElement
    {
        $elements = $driver->findElements($selector);
        foreach ($elements as $element) {
            if ($element->isDisplayed()) {
                return $element;
            }
        }
        return reset($elements);
    }
}
