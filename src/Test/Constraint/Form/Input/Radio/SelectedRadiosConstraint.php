<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use CoStack\StackTest\Test\Constraint\Form\SelectableFormConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\WebDriverRadios;

abstract class SelectedRadiosConstraint extends SelectableFormConstraint
{
    /**
     * @throws InvalidElementStateException
     */
    protected function getWebDriverObject(WebDriver $driver): WebDriverRadios
    {
        $element = $driver->findElement($this->selector);
        return new WebDriverRadios($element);
    }
}
