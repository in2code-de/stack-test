<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use CoStack\StackTest\Test\Constraint\Form\SelectableFormConstraint;
use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverRadios;

abstract class SelectedRadiosConstraint extends SelectableFormConstraint
{
    /**
     * @throws InvalidElementStateException
     */
    protected function getWebDriverObject(RemoteWebDriver $driver): WebDriverRadios
    {
        $element = $driver->findElement($this->selector);
        return new WebDriverRadios($element);
    }
}
