<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Event\Test\Failed;

class BrowserHistoryFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(RemoteWebDriver $driver): string
    {
        return implode("\n", $this->recorder->getHistory($driver));
    }

    protected function printNotification(string $fileName, Failed $event): void
    {
        echo 'Saved Browser History for failed test ' . $event->test()->id() . ' to ' . $fileName . "\n";
    }
}
