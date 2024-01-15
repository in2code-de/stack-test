<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class BrowserHistoryFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(WebDriver $driver): string
    {
        return implode("\n", $this->recorder->getHistory($driver));
    }
}
