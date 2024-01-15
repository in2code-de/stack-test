<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class PageSourceFailedSubscriber extends AbstractFailedSubscriber
{
    protected function getFileContents(WebDriver $driver): string
    {
        return $driver->getPageSource();
    }
}
