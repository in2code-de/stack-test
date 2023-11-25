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
    public function create(string $seleniumUrl = 'http://selenium-hub:4444'): Session
    {
        return new Session([
            'chrome' => $this->createChromeDriver($seleniumUrl),
            'firefox' => $this->createFirefoxDriver($seleniumUrl),
        ]);
    }

    protected function createChromeDriver(string $seleniumUrl): WebDriver
    {
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities, null, 3000);
        return new WebDriverDecorator($driver, WebDriverRecorder::getInstance());
    }

    protected function createFirefoxDriver(string $seleniumUrl): WebDriver
    {
        $desiredCapabilities = DesiredCapabilities::firefox();
        $desiredCapabilities->setCapability(WebDriverCapabilityType::ACCEPT_SSL_CERTS, true);
        $driver = RemoteWebDriver::create($seleniumUrl, $desiredCapabilities, null, 3000);
        return new WebDriverDecorator($driver, WebDriverRecorder::getInstance());
    }
}
