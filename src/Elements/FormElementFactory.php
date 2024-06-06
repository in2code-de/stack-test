<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

use Facebook\WebDriver\Remote\RemoteWebElement;

class FormElementFactory
{
    public static function fromElement(RemoteWebElement $element): FormElement
    {
        $tagName = $element->getTagName();
        $type = $element->getAttribute('type') ?? 'text';
        return match ($tagName) {
            'textarea' => new Textarea($element),
            'select' => new Select($element),
            'input' => match ($type) {
                'checkbox' => new Checkboxes($element),
                'radio' => new Radio($element),
                'text', 'password' => new Text($element),
            },
        };
    }
}
