<?php

declare(strict_types=1);

namespace CoStack\Stacktest;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

use function register_shutdown_function;
use function var_dump;

class Tester
{
    public function run(): void
    {
        $serverUrl = 'http://selenium-hub:4444';
        $desiredCapabilities = DesiredCapabilities::chrome();
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, ['args' => ['ignore-certificate-errors']]);

        $driverOne = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
        register_shutdown_function(static fn() => $driverOne->quit());

        $driverTwo = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
        register_shutdown_function(static fn() => $driverTwo->quit());

        $driverOne->get('https://web.local.co-stack-test.com/test.php');
        $driverTwo->get('https://web.local.co-stack-test.com/test.php');
        $bodyOne = $driverOne->findElements(WebDriverBy::xpath('//*'));
        $bodyTwo = $driverTwo->findElements(WebDriverBy::xpath('//*'));
        var_dump($bodyOne);
        var_dump($bodyTwo);
    }
}
