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
        if ($parameters->has('screenshotsDir')) {
            $directory = $parameters->get('screenshotsDir');
            $facade->registerSubscriber(new ScreenshotFailedSubscriber($directory));
        }
        if ($parameters->has('pageSourceDir')) {
            $directory = $parameters->get('pageSourceDir');
            $facade->registerSubscriber(new PageSourceFailedSubscriber($directory));
        }
        if ($parameters->has('historyDir')) {
            $directory = $parameters->get('historyDir');
            $facade->registerSubscriber(new BrowserHistoryFailedSubscriber($directory));
        }
    }
}
