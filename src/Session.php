<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class Session
{
    /** @param array<RemoteWebDriver> $drivers */
    public function __construct(protected readonly array $drivers)
    {
    }

    public function get(string $url): void
    {
        foreach ($this->drivers as $driver) {
            $driver->get($url);
        }
    }

    /** @return array<RemoteWebDriver> */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * Attention! Firefox always sets secure to true, whereas chrome respects the cookie settings.
     */
    public function setCookie(Cookie $cookie): void
    {
        foreach ($this->drivers as $driver) {
            $driver->manage()->addCookie($cookie);
        }
    }

    public function click(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->click();
        }
    }

    public function fillField(WebDriverBy $selector, string $string): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->clear()->sendKeys($string);
        }
    }

    public function clearField(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->clear();
        }
    }

    public function selectOption(WebDriverBy $selector, WebDriverBy|string $option): void
    {
        foreach ($this->drivers as $driver) {
            $selectElement = $driver->findElement($selector);
            $select = new WebDriverSelect($selectElement);
            if (is_string($option)) {
                $select->selectByVisibleText($option);
            } else {
                $option = $selectElement->findElement($option);
                $select->selectByValue($option->getAttribute('value'));
            }
        }
    }

    public function submitForm(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->submit();
        }
    }

    public function findElements(WebDriverBy $selector): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $elementsPerDriver[$browserName] = $driver->findElements($selector);
        }
        return new Elements($elementsPerDriver);
    }

    public function __destruct()
    {
        foreach ($this->drivers as $driver) {
            $driver->quit();
        }
    }
}
