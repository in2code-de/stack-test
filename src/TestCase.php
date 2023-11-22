<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

abstract class TestCase
{
    protected function newSession(string $seleniumUrl = 'http://selenium-hub:4444'): Session
    {
        $session = new Session();
        $session->addDriver('chrome', $this->createChromeDriver($seleniumUrl));
        $session->addDriver('firefox', $this->createFirefoxDriver($seleniumUrl));
        return $session;
    }

    protected function createChromeDriver(string $seleniumUrl): RemoteWebDriver
    {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities);
        register_shutdown_function(static fn() => $driver->quit());
        return $driver;
    }

    protected function createFirefoxDriver(string $seleniumUrl): RemoteWebDriver
    {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities);
        register_shutdown_function(static fn() => $driver->quit());
        return $driver;
    }
}
