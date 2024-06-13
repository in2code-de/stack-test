<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Script;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class ScriptReturnSame extends \CoStack\StackTest\Test\Constraint\DriverConstrain
{
    public function __construct(
        WebDriver $driver,
        protected readonly string $script,
    ) {
        parent::__construct($driver);
    }

    protected function matches(mixed $other): bool
    {
        $actual = $this->driver->executeScript($this->script);
        return $other === $actual;
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'script "%s" returns expected value',
            $this->script,
        );
    }

    public static function resolve(mixed $other, string $script = null): \Closure
    {
        if (null === $script) {
            throw new \Exception('script must not be null');
        }
        return static function (WebDriver $driver) use ($other, $script): bool {
            $constraint = new static($driver, $script);
            $result = $constraint->eval($other);
            return $result;
        };
    }
}
