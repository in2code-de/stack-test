<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use Closure;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Exception;
use Facebook\WebDriver\WebDriverBy;

abstract class DriverWithSelectorConstraint extends DriverConstrain
{
    public function __construct(
        WebDriver $driver,
        protected readonly WebDriverBy $selector,
    ) {
        parent::__construct($driver);
    }

    public static function resolve(mixed $other, WebDriverBy $selector = null): Closure
    {
        if (null === $selector) {
            throw new Exception('$selector must not be null');
        }
        return static function (WebDriver $driver) use ($other, $selector): bool {
            $constraint = new static($driver, $selector);
            $result = $constraint->eval($other);
            return $result;
        };
    }
}
