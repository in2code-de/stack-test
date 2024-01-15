<?php

declare(strict_types=1);

namespace CoStack\StackTest\Subscriber;

use CoStack\StackTest\Recorder\WebDriverRecorder;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use WeakReference;

use function CoStack\Lib\mkdir_deep;
use function dirname;

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

        mkdir_deep(dirname($fileName));
        $fileContents = $this->getFileContents($driver);
        file_put_contents($fileName, $fileContents);
    }

    protected function getLastUsedDriverFromHistory(): ?WebDriver
    {
        $history = $this->recorder->getCalls();
        if (empty($history)) {
            return null;
        }
        end($history);
        while (key($history)) {
            $frame = current($history);
            /** @var WeakReference $driverReference */
            $driverReference = $frame['driver'];
            $driver = $driverReference->get();
            if ($driver instanceof WebDriver) {
                return $driver;
            }
            prev($history);
        }
        return null;
    }

    abstract protected function getFileContents(WebDriver $driver): string;
}
