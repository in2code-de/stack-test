<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use Closure;
use CoStack\StackTest\Session\Session;
use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

abstract class SessionWithSelectorConstraint extends SessionConstrain
{
    public function __construct(
        RemoteWebDriver|Session $session,
        protected readonly WebDriverBy $selector,
    ) {
        parent::__construct($session);
    }

    public static function resolve(mixed $other, WebDriverBy $selector = null): Closure
    {
        if (null === $selector) {
            throw new Exception('$selector must not be null');
        }
        return static function (Session|RemoteWebDriver $session) use ($other, $selector): bool {
            $constraint = new static($session, $selector);
            $result = $constraint->eval($other);
            return $result;
        };
    }
}
