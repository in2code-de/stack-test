<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\Recorder\WebDriverRecorder;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use WeakReference;

abstract class AbstractFailedSubscriber implements FailedSubscriber
{
    protected readonly WebDriverRecorder $recorder;

    public function __construct(protected readonly string $directory)
    {
        $this->recorder = WebDriverRecorder::getInstance();
    }

    public function notify(Failed $event): void
    {
        $driver = $this->getLastUsedDriverFromHistory();
        if (null === $driver) {
            return;
        }

        $directory = $this->directory;
        if (!str_starts_with($directory, '/')) {
            $directory = getcwd() . '/' . $directory;
        }
        $fileName = str_replace([' ', '\\'], '_', $event->test()->id());
        $absoluteFileName = $directory . '/' . $fileName . $this->getFileExtension($driver);

        $this->ensureDirectoryExists($directory);
        $fileContents = $this->getFileContents($driver);
        file_put_contents($absoluteFileName, $fileContents);

        $this->printNotification($absoluteFileName, $event);
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

    abstract protected function getFileExtension(RemoteWebDriver $driver): string;

    abstract protected function getFileContents(RemoteWebDriver $driver): string;

    protected function printNotification(string $fileName, Failed $event): void
    {
    }

    public function ensureDirectoryExists(string $directory): void
    {
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }
    }
}
