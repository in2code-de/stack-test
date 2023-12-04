<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\WebDriverSelectInterface;

abstract class SelectableFormConstraint extends DriverWithSelectorConstraint
{
    abstract protected function getWebDriverObject(WebDriver $driver): WebDriverSelectInterface;

    protected function getSelectedOptionTexts(WebDriver $driver): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject($driver)->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getText();
        }
        return $texts;
    }

    protected function getSelectedOptionValues(WebDriver $driver): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject($driver)->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getAttribute('value');
        }
        return $texts;
    }
}
