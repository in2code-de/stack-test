<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Event\Test\Failed;

class ScreenshotFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileExtension(RemoteWebDriver $driver): string
    {
        return '.screenshot.jpg';
    }

    protected function getFileContents(RemoteWebDriver $driver): string
    {
        return $driver->takeScreenshot();
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Screenshot for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
