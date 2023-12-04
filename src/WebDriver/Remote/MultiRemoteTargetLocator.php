<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Facebook\WebDriver\Remote\RemoteTargetLocator;
use Facebook\WebDriver\WebDriverAlert;

class MultiRemoteTargetLocator extends RemoteTargetLocator
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(public readonly MultiWebDriver $multiWebDriver)
    {
    }

    public function defaultContent(): MultiWebDriver
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->defaultContent();
        }
        return $this->multiWebDriver;
    }

    public function frame($frame): MultiWebDriver
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->frame($frame);
        }
        return $this->multiWebDriver;
    }

    public function parent(): MultiWebDriver
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->parent();
        }
        return $this->multiWebDriver;
    }

    public function window($handle): MultiWebDriver
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->window($handle);
        }
        return $this->multiWebDriver;
    }

    public function newWindow($windowType = self::WINDOW_TYPE_TAB): MultiWebDriver
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->newWindow($windowType);
        }
        return $this->multiWebDriver;
    }

    public function alert(): MultiWebDriverAlert
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->alert();
        }
        return new MultiWebDriverAlert($this->multiWebDriver);
    }

    public function activeElement(): MultiRemoteWebElement
    {
        $elements = [];
        foreach ($this->multiWebDriver->drivers as $driver) {
            $elements[] = $driver->switchTo()->activeElement();
        }
        return new MultiRemoteWebElement($elements);
    }
}
