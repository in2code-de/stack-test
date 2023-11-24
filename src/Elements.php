<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;

class Elements
{
    /** @param array<string, array<WebDriverElement>> $elementsPerDriver */
    public function __construct(protected array $elementsPerDriver)
    {
    }

    /** @return array<string, array<WebDriverElement>> */
    public function getElementsPerDriver(): array
    {
        return $this->elementsPerDriver;
    }

    public function clear(): static
    {
        foreach ($this->elementsPerDriver as $elements) {
            foreach ($elements as $element) {
                $element->clear();
            }
        }
        return $this;
    }

    public function click(): static
    {
        foreach ($this->elementsPerDriver as $elements) {
            foreach ($elements as $element) {
                $element->click();
            }
        }
        return $this;
    }

    public function sendKeys($value): static
    {
        foreach ($this->elementsPerDriver as $elements) {
            foreach ($elements as $element) {
                $element->sendKeys($value);
            }
        }
        return $this;
    }

    public function submit(): static
    {
        foreach ($this->elementsPerDriver as $elements) {
            foreach ($elements as $element) {
                $element->submit();
            }
        }
        return $this;
    }

    public function findElement(WebDriverBy $locator): Element
    {
        $elementsPerDriver = [];
        foreach ($this->elementsPerDriver as $browserName => $elements) {
            foreach ($elements as $element) {
                $elementsPerDriver[$browserName] = $element->findElements($locator);
            }
        }
        return new Element($elementsPerDriver);
    }

    public function findElements(WebDriverBy $locator): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->elementsPerDriver as $browserName => $elements) {
            foreach ($elements as $element) {
                $elementsPerDriver[$browserName] = $element->findElements($locator);
            }
        }
        return new Elements($elementsPerDriver);
    }
}
