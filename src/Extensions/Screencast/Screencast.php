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
use function CoStack\Lib\mkdir_deep;
use function dirname;
use function fopen;
use function fwrite;
use function getenv;
use function hash;
use function json_encode;
use function preg_replace_callback;
use function sprintf;
use function str_replace;
use function str_starts_with;

use const PHP_EOL;

class Screencast implements Extension
{
    protected Configuration $configuration;
    protected array $parameters = [
        'network' => '',
        'image' => '',
        'path' => '',
        'file' => '',
        'browser-container-name' => '',
        'logs' => '',
    ];
    /** @var array<Process> */
    protected array $processes = [];
    protected $logFile;

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $this->configuration = $configuration;
        foreach (array_keys($this->parameters) as $parameter) {
            if (!$parameters->has($parameter) || empty($this->parameters[$parameter] = $parameters->get($parameter))) {
                throw new Exception('Parameter ' . $parameter . ' is not set');
            }
        }
        $logsPath = $this->parameters['logs'];
        if (!str_starts_with($logsPath, '/')) {
            $basePath = dirname($configuration->configurationFile());
            $logsPath = concat_paths($basePath, $logsPath);
        }
        $this->parameters['logs'] = $logsPath;

        mkdir_deep(dirname($this->parameters['logs']));
        $this->logFile = fopen($this->parameters['logs'], 'wb');
        fwrite(
            $this->logFile,
            'Started screen recorder with settings:' . PHP_EOL
            . json_encode($this->parameters) . PHP_EOL,
        );

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
        $path = preg_replace_callback('/\$([\w_]+)/', static fn(array $env): string => getenv($env[1]), $path);
        if (!str_starts_with($path, '/')) {
            $basePath = dirname($this->configuration->configurationFile());
            $path = concat_paths($basePath, $path);
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
            '--platform=linux/amd64',
            '--network=' . $this->parameters['network'],
            '-v' . $path . ':/videos',
            '-eDISPLAY_CONTAINER_NAME=' . $this->parameters['browser-container-name'],
            '-eFILE_NAME=' . $file,
            $this->parameters['image'],
        ];
        $process = new Process($command);
        $this->processes[$castId] = $process;

        fwrite(
            $this->logFile,
            sprintf(
                'Recording %s (%s::%s) screen with docker: %s',
                $castId,
                $class,
                $method,
                $process->getCommandLine(),
            ) . PHP_EOL,
        );

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
        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        $exitCode = $process->getExitCode();

        fwrite(
            $this->logFile,
            sprintf('Recording %s ended with code %d. Output and Errors:', $castId, $exitCode) . PHP_EOL
            . 'Output:' . PHP_EOL
            . $output . PHP_EOL
            . '---' . PHP_EOL
            . 'Errors:' . PHP_EOL
            . $errorOutput . PHP_EOL
            . '---END' . PHP_EOL
            . PHP_EOL,
        );
    }
}
