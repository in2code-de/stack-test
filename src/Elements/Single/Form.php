<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;

class Form
{
    public function __construct(protected readonly RemoteWebElement $formElement)
    {
    }

    public function getElement(WebDriverBy $selector): RemoteWebElement
    {
        return $this->formElement->findElement($selector);
    }

    public function getElementByName(string $name): RemoteWebElement
    {
        return $this->getElement(WebDriverBy::name($name));
    }

    public function getElements(?WebDriverBy $selector = null): array
    {
        $selector ??= WebDriverBy::xpath(
            '//*[(self::input or self::select or self::textarea) and @name and not(@type="hidden") and not(@hidden) and not(@disabled)]',
        );
        return $this->formElement->findElements($selector);
    }

    public function getData(): array
    {
        $elements = $this->getElements();
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
        foreach ($data as $name => $value) {
            $element = $this->getElementByName($name);
            $formElement = FormElementFactory::fromElement($element);
            $formElement->setValue($value);
        }
    }
}
