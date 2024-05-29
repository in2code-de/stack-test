<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver;

use CoStack\StackTest\WebDriver\Remote\MultiWebDriver;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;

use function bin2hex;
use function random_bytes;

class WebDriverFactory
{
    public static function createMultiDriver(
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): MultiWebDriver {
        $sessionId = bin2hex(random_bytes(16));
        return new MultiWebDriver($sessionId, [
            'chrome' => self::createChromeDriver($seleniumUrl),
            'firefox' => self::createFirefoxDriver($seleniumUrl),
        ]);
    }

    public static function createChromeDriver(
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return self::createDriver($desiredCapabilities, $seleniumUrl);
    }

    public static function createFirefoxDriver(
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        return self::createDriver($desiredCapabilities, $seleniumUrl);
    }

    public static function createDriver(
        DesiredCapabilities $desiredCapabilities,
        string $seleniumUrl = 'http://selenium-hub:4444',
    ): WebDriver {
        return WebDriver::create($seleniumUrl, $desiredCapabilities);
    }
}
