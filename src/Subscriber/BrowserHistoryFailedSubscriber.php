<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Event\Test\Failed;

class BrowserHistoryFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(WebDriver $driver): string
    {
        return implode("\n", $this->recorder->getHistory($driver));
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Browser History for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
