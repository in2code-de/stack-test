<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Test\Constraint\SessionWithSelectorConstraint;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverSelectInterface;

abstract class SelectableFormConstraint extends SessionWithSelectorConstraint
{
    abstract protected function getWebDriverObject(RemoteWebDriver $driver): WebDriverSelectInterface;

    protected function getSelectedOptionTexts(RemoteWebDriver $driver): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject($driver)->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getText();
        }
        return $texts;
    }

    protected function getSelectedOptionValues(RemoteWebDriver $driver): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject($driver)->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getAttribute('value');
        }
        return $texts;
    }
}
