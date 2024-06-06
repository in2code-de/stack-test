<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

class WebDriverFactory
{
    public static function createChromeDriver(
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return WebDriver::create($seleniumUrl, $desiredCapabilities);
    }

    public static function createFirefoxDriver(
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return WebDriver::create($seleniumUrl, $desiredCapabilities);
    }
}
