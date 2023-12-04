<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\Test\Constraint\Form\SelectableFormConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\InvalidElementStateException;
use Facebook\WebDriver\WebDriverCheckboxes;

abstract class SelectedCheckboxesConstraint extends SelectableFormConstraint
{
    /**
     * @throws InvalidElementStateException
     */
    protected function getWebDriverObject(WebDriver $driver): WebDriverCheckboxes
    {
        $element = $driver->findElement($this->selector);
        return new WebDriverCheckboxes($element);
    }
}
