<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Facebook\WebDriver\WebDriverTimeouts;

class MultiWebDriverTimeouts extends WebDriverTimeouts
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        public readonly MultiWebDriver $driver,
    ) {}

    public function implicitlyWait($seconds): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->timeouts()->implicitlyWait($seconds);
        }
        return $this;
    }

    public function setScriptTimeout($seconds): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->timeouts()->setScriptTimeout($seconds);
        }
        return $this;
    }

    public function pageLoadTimeout($seconds): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->timeouts()->pageLoadTimeout($seconds);
        }
        return $this;
    }
}
