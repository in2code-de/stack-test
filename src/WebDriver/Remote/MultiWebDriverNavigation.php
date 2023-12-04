<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Facebook\WebDriver\WebDriverNavigation;

class MultiWebDriverNavigation extends WebDriverNavigation
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(public readonly MultiWebDriver $driver)
    {
    }

    public function back(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->navigate()->back();
        }
        return $this;
    }

    public function forward(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->navigate()->forward();
        }
        return $this;
    }

    public function refresh(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->navigate()->refresh();
        }
        return $this;
    }

    public function to($url): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->navigate()->to($url);
        }
        return $this;
    }
}
