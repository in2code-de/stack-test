<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\WebDriverBy;

class Form
{
    public function __construct(public readonly WebDriver $driver, public readonly WebDriverBy $by)
    {
    }

    public function getData(): array
    {
        $selector ??= WebDriverBy::xpath(
            '//*[(self::input or self::select or self::textarea) and @name and not(@type="hidden") and not(@hidden) and not(@disabled)]',
        );
        $elements = $this->driver->findElements($selector);
        $formData = [];
        foreach ($elements as $element) {
            $name = $element->getAttribute('name');
            if (isset($formData[$name]) && in_array($element->getAttribute('type'), ['checkbox', 'radio'])) {
                continue;
            }
            $formElement = FormElementFactory::fromElement($element);
            $formData[$name] = $formElement->getValue();
        }
        return $formData;
    }

    public function setData(array $data): void
    {
        $form = $this->driver->findElement($this->by);
        foreach ($data as $name => $value) {
            $element = $form->findElement(WebDriverBy::name($name));
            $formElement = FormElementFactory::fromElement($element);
            $formElement->setValue($value);
        }
    }
}
