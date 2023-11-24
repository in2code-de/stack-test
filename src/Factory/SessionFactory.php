<?php

declare(strict_types=1);

namespace CoStack\StackTest\Factory;

use CoStack\StackTest\Session;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class SessionFactory
{
    public function create(string $seleniumUrl = 'http://selenium-hub:4444'): Session
    {
        return new Session([
            'chrome' => $this->createChromeDriver($seleniumUrl),
            'firefox' => $this->createFirefoxDriver($seleniumUrl),
        ]);
    }

    protected function createChromeDriver(string $seleniumUrl): RemoteWebDriver
    {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities, null, 3000);
        // Safety fallback to eliminate sessions even if PHP fails
        register_shutdown_function(static fn() => $driver->quit());
        return $driver;
    }

    protected function createFirefoxDriver(string $seleniumUrl): RemoteWebDriver
    {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities, null, 3000);
        // Safety fallback to eliminate sessions even if PHP fails
        register_shutdown_function(static fn() => $driver->quit());
        return $driver;
    }
}
