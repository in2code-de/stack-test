<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverCheckboxes;

class Checkboxes implements FormElement
{
    protected WebDriverCheckboxes $checkboxes;

    public function __construct(
        public readonly RemoteWebElement $element,
    ) {
        $this->checkboxes = new WebDriverCheckboxes($this->element);
    }

    public function getValue(): string|array
    {
        $value = [];
        $selectedOptions = $this->checkboxes->getAllSelectedOptions();
        foreach ($selectedOptions as $selectedOption) {
            $value[] = $selectedOption->getAttribute('value');
        }
        return $value;
    }

    public function setValue(array|string $value): void
    {
        if (!is_array($value)) {
            throw new Exception('Value to set for checkboxes must be array');
        }
        $this->checkboxes->deselectAll();
        foreach ($value as $var) {
            $this->checkboxes->selectByValue($var);
        }
    }

    public function isMultiple(): bool
    {
        return $this->checkboxes->isMultiple();
    }

    public function deselectAll(): void
    {
        $this->checkboxes->deselectAll();
    }

    public function deselectByIndex($index): void
    {
        $this->checkboxes->deselectByIndex($index);
    }

    public function deselectByValue($value): void
    {
        $this->checkboxes->deselectByValue($value);
    }

    public function deselectByVisibleText($text): void
    {
        $this->checkboxes->deselectByVisibleText($text);
    }

    public function deselectByVisiblePartialText($text): void
    {
        $this->checkboxes->deselectByVisiblePartialText($text);
    }

    public function selectAll(): void
    {
        foreach ($this->checkboxes->getOptions() as $option) {
            if (!$option->isSelected()) {
                $option->click();
            }
        }
    }

    public function selectByIndex($index): void
    {
        $this->checkboxes->selectByIndex($index);
    }

    public function selectByValue($value): void
    {
        $this->checkboxes->selectByValue($value);
    }

    public function selectByVisibleText($text): void
    {
        $this->checkboxes->selectByVisibleText($text);
    }

    public function selectByVisiblePartialText($text): void
    {
        $this->checkboxes->selectByVisiblePartialText($text);
    }
}
