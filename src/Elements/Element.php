<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;

class Element
{
    /** @param array<string, WebDriverElement> $elementPerDriver */
    public function __construct(protected array $elementPerDriver)
    {
    }

    /** @return array<string, WebDriverElement> */
    public function getElementPerDriver(): array
    {
        return $this->elementPerDriver;
    }

    public function clear(): static
    {
        foreach ($this->elementPerDriver as $element) {
            $element->clear();
        }
        return $this;
    }

    public function click(): static
    {
        foreach ($this->elementPerDriver as $element) {
            $element->click();
        }
        return $this;
    }

    public function sendKeys($value): static
    {
        foreach ($this->elementPerDriver as $element) {
            $element->sendKeys($value);
        }
        return $this;
    }

    public function submit(): static
    {
        foreach ($this->elementPerDriver as $element) {
            $element->submit();
        }
        return $this;
    }

    public function findElement(WebDriverBy $locator): Element
    {
        $elementPerDriver = [];
        foreach ($this->elementPerDriver as $browserName => $element) {
            $elementPerDriver[$browserName] = $element->findElement($locator);
        }
        return new Element($elementPerDriver);
    }

    public function findElements(WebDriverBy $locator): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->elementPerDriver as $browserName => $elements) {
            foreach ($elements as $element) {
                $elementsPerDriver[$browserName] = $element->findElements($locator);
            }
        }
        return new Elements($elementsPerDriver);
    }
}
