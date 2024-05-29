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
}
