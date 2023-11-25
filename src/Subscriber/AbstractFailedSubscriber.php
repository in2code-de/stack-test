<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\Recorder\WebDriverRecorder;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use WeakReference;

abstract class AbstractFailedSubscriber implements FailedSubscriber
{
    protected readonly WebDriverRecorder $recorder;

    public function __construct(
        protected readonly string $fileName,
        protected readonly Configuration $configuration,
        protected readonly Facade $facade,
        protected readonly ParameterCollection $parameters,
    ) {
        $this->recorder = WebDriverRecorder::getInstance();
    }

    public function notify(Failed $event): void
    {
        $driver = $this->getLastUsedDriverFromHistory();
        if (null === $driver) {
            return;
        }

        $test = $event->test();
        if (!$test instanceof TestMethod) {
            echo 'Can not export information for tests which are not test methods';
            return;
        }
        $testId = str_replace([' ', '\\'], '_', $test->id());

        $fileName = str_replace(
            [
                '{testId}',
                '{testClass}',
                '{testMethod}',
                '{seed}',
            ],
            [
                $testId,
                str_replace('\\', '_', $test->className()),
                $test->methodName(),
                $this->configuration->randomOrderSeed(),
            ],
            $this->fileName,
        );
        if (!str_starts_with($fileName, '/')) {
            $fileName = getcwd() . '/' . $fileName;
        }

        $this->ensureDirectoryExists($fileName);
        $fileContents = $this->getFileContents($driver);
        file_put_contents($fileName, $fileContents);

        $this->printNotification($fileName, $event);
    }

    protected function getLastUsedDriverFromHistory(): ?RemoteWebDriver
    {
        $history = $this->recorder->getCalls();
        if (empty($history)) {
            return null;
        }
        $driver = null;
        end($history);
        while (key($history)) {
            $frame = current($history);
            /** @var WeakReference $driverReference */
            $driverReference = $frame['driver'];
            $driver = $driverReference->get();
            if ($driver instanceof RemoteWebDriver) {
                break;
            }
            prev($history);
        }
        return $driver;
    }

    abstract protected function getFileContents(RemoteWebDriver $driver): string;

    protected function printNotification(string $fileName, Failed $event): void
    {
    }

    public function ensureDirectoryExists(string $fileName): void
    {
        $directory = dirname($fileName);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }
}
