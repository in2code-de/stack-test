<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebElement;

class Text implements FormElement
{
    public function __construct(
        public readonly RemoteWebElement $element,
    ) {}

    public function getValue(): string|array
    {
        $value = $this->element->getAttribute('value');
        return $value;
    }

    public function setValue(array|string $value): void
    {
        if (!is_string($value)) {
            throw new Exception('Value to set for input type text must be string');
        }
        $this->element->clear();
        $this->element->sendKeys($value);
    }
}
