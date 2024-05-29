<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverSelect;

use function is_array;
use function is_string;

class Select implements FormElement
{
    protected WebDriverSelect $select;

    public function __construct(
        public readonly RemoteWebElement $element,
    ) {
        $this->select = new WebDriverSelect($this->element);
    }

    public function getValue(): string|array
    {
        if ($this->select->isMultiple()) {
            $value = [];
            $selectedOptions = $this->select->getAllSelectedOptions();
            foreach ($selectedOptions as $selectedOption) {
                $value[] = $selectedOption->getAttribute('value');
            }
            return $value;
        }
        $selectedOption = $this->select->getFirstSelectedOption();
        $value = $selectedOption->getAttribute('value');
        return $value;
    }

    public function setValue(array|string $value): void
    {
        if ($this->select->isMultiple()) {
            if (!is_array($value)) {
                throw new Exception('Value to set for form select with multiple must be array');
            }
            $this->select->deselectAll();
            foreach ($value as $var) {
                $this->select->selectByValue($var);
            }
            return;
        }
        if (!is_string($value)) {
            throw new Exception('Value to set for form select without multiple must be string');
        }
        $this->select->selectByValue($value);
    }

    public function setValueByText(array|string $value): void
    {
        if ($this->select->isMultiple()) {
            if (!is_array($value)) {
                throw new Exception('Value to set for form select with multiple must be array');
            }
            $this->select->deselectAll();
            foreach ($value as $var) {
                $this->select->selectByVisibleText($var);
            }
            return;
        }
        if (!is_string($value)) {
            throw new Exception('Value to set for form select without multiple must be string');
        }
        $this->select->selectByVisibleText($value);
    }
}
