<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use CoStack\StackTest\Recorder\WebDriverRecorder;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\WebDriverCommand;
use Facebook\WebDriver\Remote\WebDriverResponse;

class RecordingCommandExecutor extends HttpCommandExecutor
{
    public readonly WebDriverRecorder $recorder;

    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(HttpCommandExecutor $inner)
    {
        $this->url = $inner->url;
        $this->curl = $inner->curl;
        self::$commands = $inner::$commands;
        self::$w3cCompliantCommands = $inner::$w3cCompliantCommands;
        $this->recorder = WebDriverRecorder::getInstance();
    }

    public function execute(WebDriverCommand $command): WebDriverResponse
    {
        $this->recorder->record();
        return parent::execute($command);
    }
}
