<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Event\Test\Failed;

class PageSourceFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileExtension(RemoteWebDriver $driver): string
    {
        return '.page_source.html';
    }

    protected function getFileContents(RemoteWebDriver $driver): string
    {
        return $driver->getPageSource();
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Page Source for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
