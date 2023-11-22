<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Session
{
    /** @var array<RemoteWebDriver> */
    protected array $drivers = [];

    public function addDriver(string $id, RemoteWebDriver $driver): void
    {
        $this->drivers[$id] = $driver;
    }

    public function get(string $url): void
    {
        foreach ($this->drivers as $driver) {
            $driver->get($url);
        }
    }

    public function see(string $string): void
    {
        foreach ($this->drivers as $driver) {
            foreach ($driver->findElements(WebDriverBy::xpath('//*')) as $element) {
                if ($element->isDisplayed()) {
                    $contents = $element->getText();
                    if (!str_contains($contents, $string)) {
                        throw new \Exception('Assertion failed that page contains ' . $string . ' in contents ' . $contents
                        );
                    }

                    break;
                }
            }
        }
    }
}
