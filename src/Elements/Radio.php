<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverRadios;

class Radio implements FormElement
{
    protected WebDriverRadios $radios;

    public function __construct(
        public readonly RemoteWebElement $element,
    ) {
        $this->radios = new WebDriverRadios($this->element);
    }

    public function getValue(): string|array
    {
        $selectedOption = $this->radios->getFirstSelectedOption();
        $value = $selectedOption->getAttribute('value');
        return $value;
    }

    public function setValue(array|string $value): void
    {
        if (!is_string($value)) {
            throw new Exception('Value to set for radio must be string');
        }
        $this->radios->selectByValue($value);
    }
}
