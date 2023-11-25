<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Subscriber\BrowserHistoryFailedSubscriber;
use CoStack\StackTest\Subscriber\PageSourceFailedSubscriber;
use CoStack\StackTest\Subscriber\ScreenshotFailedSubscriber;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

class Bootstrap implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if ($parameters->has('screenshot')) {
            $fileName = $parameters->get('screenshot');
            $facade->registerSubscriber(
                new ScreenshotFailedSubscriber($fileName, $configuration, $facade, $parameters),
            );
        }
        if ($parameters->has('pageSource')) {
            $fileName = $parameters->get('pageSource');
            $facade->registerSubscriber(
                new PageSourceFailedSubscriber($fileName, $configuration, $facade, $parameters),
            );
        }
        if ($parameters->has('history')) {
            $fileName = $parameters->get('history');
            $facade->registerSubscriber(
                new BrowserHistoryFailedSubscriber($fileName, $configuration, $facade, $parameters),
            );
        }
    }
}
