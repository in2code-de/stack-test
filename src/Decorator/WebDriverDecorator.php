<?php

declare(strict_types=1);

namespace CoStack\StackTest\Decorator;

use CoStack\StackTest\Recorder\WebDriverRecorder;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class WebDriverDecorator extends RemoteWebDriver
{
    public function __construct(public readonly RemoteWebDriver $inner, public readonly WebDriverRecorder $recorder)
    {
        parent::__construct($inner->executor, $inner->sessionID, $inner->capabilities, $inner->isW3cCompliant);

        // Safety fallback to eliminate sessions even if PHP fails.
        register_shutdown_function(fn() => $this->quit());
    }

    public function executeScript($script, $arguments = [])
    {
        $this->recorder->record();
        return parent::executeScript($script, $arguments);
    }

    public function executeAsyncScript($script, $arguments = [])
    {
        $this->recorder->record();
        return parent::executeAsyncScript($script, $arguments);
    }

    public function close()
    {
        $this->recorder->record();
        return parent::close();
    }

    public function get($url)
    {
        $this->recorder->record();
        return parent::get($url);
    }

    public function getCurrentURL()
    {
        $this->recorder->record();
        return parent::getCurrentURL();
    }

    public function getPageSource()
    {
        $this->recorder->record();
        return parent::getPageSource();
    }

    public function getTitle()
    {
        $this->recorder->record();
        return parent::getTitle();
    }

    public function getWindowHandle()
    {
        $this->recorder->record();
        return parent::getWindowHandle();
    }

    public function getWindowHandles()
    {
        $this->recorder->record();
        return parent::getWindowHandles();
    }

    public function quit()
    {
        $this->recorder->record();
        $this->inner->quit();
        $this->executor = null;
    }

    public function takeScreenshot($save_as = null)
    {
        $this->recorder->record();
        return parent::takeScreenshot($save_as);
    }

    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250)
    {
        $this->recorder->record();
        return parent::wait($timeout_in_second, $interval_in_millisecond);
    }

    public function manage()
    {
        $this->recorder->record();
        return parent::manage();
    }

    public function navigate()
    {
        $this->recorder->record();
        return parent::navigate();
    }

    public function switchTo()
    {
        $this->recorder->record();
        return parent::switchTo();
    }

    public function execute($command_name, $params = [])
    {
        $this->recorder->record();
        return parent::execute($command_name, $params);
    }

    public function getKeyboard()
    {
        $this->recorder->record();
        return parent::getKeyboard();
    }

    public function getMouse()
    {
        $this->recorder->record();
        return parent::getMouse();
    }

    public function findElement(WebDriverBy $by)
    {
        $this->recorder->record();
        return parent::findElement($by);
    }

    public function findElements(WebDriverBy $by)
    {
        $this->recorder->record();
        return parent::findElements($by);
    }
}
