<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver;

use CoStack\StackTest\Pattern\Singleton;
use CoStack\StackTest\WebDriver\Remote\MultiWebDriver;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class Factory
{
    use Singleton;

    /** @var array<WebDriver> */
    protected array $drivers = [];

    public function createMultiDriver(
        string $sessionId = null,
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): MultiWebDriver {
        $sessionId ??= bin2hex(random_bytes(16));
        return $this->drivers['multi'][$sessionId] ??= new MultiWebDriver($sessionId, [
            'chrome' => $this->createChromeDriver($sessionId, $seleniumUrl),
            'firefox' => $this->createFirefoxDriver($sessionId, $seleniumUrl),
        ]);
    }

    public function createChromeDriver(
        string $sessionId = null,
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return $this->drivers['chrome'][$sessionId] ??= $this->createDriver(
            $desiredCapabilities,
            $sessionId,
            $seleniumUrl,
        );
    }

    public function createFirefoxDriver(
        string $sessionId = null,
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return $this->drivers['firefox'][$sessionId] ??= $this->createDriver(
            $desiredCapabilities,
            $sessionId,
            $seleniumUrl,
        );
    }

    public function createDriver(
        DesiredCapabilities $desiredCapabilities,
        string $sessionId = null,
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        return $this->drivers[$desiredCapabilities->getBrowserName()][$sessionId] ??= WebDriver::create(
            $seleniumUrl,
            $desiredCapabilities,
        );
    }

    public function forgetDriver(WebDriver $driver): void
    {
        if ($driver instanceof MultiWebDriver) {
            unset($this->drivers['multi'][$driver->sessionId]);
            return;
        }
        foreach ($this->drivers[$driver->browserName] as $sessionId => $knownDriver) {
            if ($knownDriver === $driver) {
                unset($this->drivers[$driver->browserName][$sessionId]);
            }
        }
    }
}
