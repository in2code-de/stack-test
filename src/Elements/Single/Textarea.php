<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

use Facebook\WebDriver\Remote\RemoteWebElement;

class Textarea implements FormElement
{
    public function __construct(
        public readonly RemoteWebElement $element,
    ) {}

    public function getValue(): string|array
    {
        return $this->element->getAttribute('value');
    }

    public function setValue(array|string $value): void
    {
        $this->element->clear();
        $this->element->sendKeys($value);
    }
}
