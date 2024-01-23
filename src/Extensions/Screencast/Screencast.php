<?php

declare(strict_types=1);

namespace CoStack\StackTest\Extensions\Screencast;

use CoStack\StackTest\Extensions\Screencast\Subscriber\StartRecordingForTest;
use CoStack\StackTest\Extensions\Screencast\Subscriber\StopRecordingForTest;
use Exception;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Symfony\Component\Process\Process;

use function array_keys;
use function CoStack\Lib\concat_paths;
use function getcwd;
use function hash;
use function json_encode;
use function str_replace;
use function str_starts_with;

class Screencast implements Extension
{
    protected Configuration $configuration;
    protected array $parameters = [
        'network' => '',
        'image' => '',
        'path' => '',
        'file' => '',
        'browser-container-name' => '',
    ];
    /** @var array<Process> */
    protected array $processes = [];

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $this->configuration = $configuration;
        foreach (array_keys($this->parameters) as $parameter) {
            if (!$parameters->has($parameter) || empty($this->parameters[$parameter] = $parameters->get($parameter))) {
                throw new Exception('Parameter ' . $parameter . ' is not set');
            }
        }

        $facade->registerSubscriber(new StartRecordingForTest($this));
        $facade->registerSubscriber(new StopRecordingForTest($this));
    }

    public function start(Prepared $event): void
    {
        /** @var TestMethod $testMethod */
        $testMethod = $event->test();
        $method = $testMethod->methodName();
        $class = $testMethod->className();

        $castId = hash('sha1', json_encode([$class, $method]));

        $path = $this->parameters['path'];

        $path = str_replace(
            [
                '{testClass}',
                '{testMethod}',
                '{seed}',
                '{browser-container-name}',
            ],
            [
                str_replace('\\', '_', $class),
                $method,
                $this->configuration->randomOrderSeed(),
                $this->parameters['browser-container-name'],
            ],
            $path,
        );
        if (!str_starts_with($path, '/')) {
            $path = concat_paths(getcwd(), $path);
        }

        $file = $this->parameters['file'];
        $file = str_replace(
            [
                '{testClass}',
                '{testMethod}',
                '{seed}',
                '{browser-container-name}',
            ],
            [
                str_replace('\\', '_', $class),
                $method,
                $this->configuration->randomOrderSeed(),
                $this->parameters['browser-container-name'],
            ],
            $file,
        );

        $command = [
            'docker',
            'run',
            '--rm',
            '--network=' . $this->parameters['network'],
            '-v' . $path . ':/videos',
            '-eDISPLAY_CONTAINER_NAME=' . $this->parameters['browser-container-name'],
            '-eFILE_NAME=' . $file,
            $this->parameters['image'],
        ];
        $process = new Process($command);
        $this->processes[$castId] = $process;
        $process->start();
    }

    public function stop(Finished $event)
    {
        /** @var TestMethod $test */
        $test = $event->test();
        $class = $test->className();
        $method = $test->methodName();
        $castId = hash('sha1', json_encode([$class, $method]));
        $process = $this->processes[$castId];
        unset($this->processes[$castId]);
        $process->stop();
    }
}
