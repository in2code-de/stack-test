<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Closure;
use Facebook\WebDriver\WebDriverOptions;

class MultiWebDriverOptions extends WebDriverOptions
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(public readonly MultiWebDriver $driver)
    {
    }

    public function addCookie($cookie): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->addCookie($cookie);
        }
        return $this;
    }

    public function deleteAllCookies(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->deleteAllCookies();
        }
        return $this;
    }

    public function deleteCookieNamed($name): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->deleteCookieNamed($name);
        }
        return $this;
    }

    public function getCookieNamed($name): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachCookieNamed instead.',
        );
    }

    public function foreachCookieNamed(Closure $callback, string $name): void
    {
        foreach ($this->driver->drivers as $driver) {
            $callback($driver->manage()->getCookieNamed($name));
        }
    }

    public function getCookies(): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachCookies instead.',
        );
    }

    public function foreachCookies(Closure $callback): void
    {
        foreach ($this->driver->drivers as $driver) {
            $callback($driver->manage()->getCookies());
        }
    }

    public function timeouts(): MultiWebDriverTimeouts
    {
        return new MultiWebDriverTimeouts($this->driver);
    }

    public function window(): MultiWebDriverWindow
    {
        return new MultiWebDriverWindow($this->driver);
    }

    public function getLog($log_type)
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachLog instead.',
        );
    }

    public function foreachLog(Closure $closure, $log_type): void
    {
        foreach ($this->driver->drivers as $driver) {
            $closure($driver->manage()->getLog($log_type));
        }
    }

    public function getAvailableLogTypes(): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachAvailableLogTypes instead.',
        );
    }

    public function foreachgetAvailableLogTypes(Closure $closure): void
    {
        foreach ($this->driver->drivers as $driver) {
            $closure($driver->manage()->getAvailableLogTypes());
        }
    }
}
