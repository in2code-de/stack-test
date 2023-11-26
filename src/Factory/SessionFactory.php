<?php

declare(strict_types=1);

namespace CoStack\StackTest\Factory;

use CoStack\StackTest\Decorator\WebDriverDecorator;
use CoStack\StackTest\Recorder\WebDriverRecorder;
use CoStack\StackTest\Session;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriver;

class SessionFactory
{
    protected static array $sessions = [];

    public function create(string $sessionId = null, string $seleniumUrl = 'http://selenium-hub:4444'): Session
    {
        $sessionId ??= bin2hex(random_bytes(16));
        return self::$sessions[$sessionId] ??= new Session($sessionId, [
            'chrome' => $this->createChromeDriver($seleniumUrl),
            'firefox' => $this->createFirefoxDriver($seleniumUrl),
        ]);
    }

    protected function createChromeDriver(string $seleniumUrl): WebDriver
    {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities);
        return new WebDriverDecorator($driver, WebDriverRecorder::getInstance());
    }

    protected function createFirefoxDriver(string $seleniumUrl): WebDriver
    {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities);
        return new WebDriverDecorator($driver, WebDriverRecorder::getInstance());
    }
}
