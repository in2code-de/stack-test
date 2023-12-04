<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Event\Test\Failed;

class ScreenshotFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(WebDriver $driver): string
    {
        return $driver->takeScreenshot();
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Screenshot for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
