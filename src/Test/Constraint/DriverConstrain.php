<?php

/**
 * @noinspection PhpUnnecessaryLocalVariableInspection
 * @noinspection OneTimeUseVariablesInspection
 */

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use Closure;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\Constraint\Constraint;

use function CoStack\StackTest\resolve;
use function is_array;
use function reset;

abstract class DriverConstrain extends Constraint
{
    protected array $driverResults = [];

    public function __construct(
        public readonly WebDriver $driver,
    ) {}

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

    protected function resolveSelectorsInOtherToText(mixed $other): mixed
    {
        if ($other instanceof WebDriverBy) {
            $element = $this->driver->findElement($other);
            $other = $element->getText();
        }
        if (is_array($other)) {
            foreach ($other as $index => $value) {
                $other[$index] = resolve($this->driver, $value);
            }
        }
        return $other;
    }

    protected function resolveSelectorsInOtherToValue(mixed $other): mixed
    {
        if ($other instanceof WebDriverBy) {
            $element = $this->driver->findElement($other);
            $other = $element->getAttribute('value');
        }
        if (is_array($other)) {
            foreach ($other as $index => $value) {
                $other[$index] = resolve($this->driver, $value);
            }
        }
        return $other;
    }

    protected function getFirstVisibleElement(WebDriverBy $selector): RemoteWebElement
    {
        $elements = $this->driver->findElements($selector);
        foreach ($elements as $element) {
            if ($element->isDisplayed()) {
                return $element;
            }
        }
        return reset($elements);
    }
}
