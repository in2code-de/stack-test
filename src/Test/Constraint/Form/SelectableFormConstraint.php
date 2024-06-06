<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use Facebook\WebDriver\WebDriverSelectInterface;

abstract class SelectableFormConstraint extends DriverWithSelectorConstraint
{
    abstract protected function getWebDriverObject(): WebDriverSelectInterface;

    protected function getSelectedOptionTexts(): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject()->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getText();
        }
        return $texts;
    }

    protected function getSelectedOptionValues(): array
    {
        $texts = [];
        $selectedOptions = $this->getWebDriverObject()->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $texts[] = $selectedOption->getAttribute('value');
        }
        return $texts;
    }
}
