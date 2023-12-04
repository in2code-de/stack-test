<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Event\Test\Failed;

class PageSourceFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(WebDriver $driver): string
    {
        return $driver->getPageSource();
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Page Source for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
