<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Parallel;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;

class Element
{
    /** @param non-empty-array<string, WebDriverElement> $elementPerDriver */
    public function __construct(public array $elementPerDriver)
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
            $webDriverElement = $element->findElement($locator);
            $elementPerDriver[$browserName] = $webDriverElement;
        }
        return new Element($elementPerDriver);
    }

    public function findFirstVisibleElement(WebDriverBy $locator): Element
    {
        $elementPerDriver = [];
        foreach ($this->elementPerDriver as $browserName => $element) {
            $webDriverElements = $element->findElements($locator);
            foreach ($webDriverElements as $webDriverElement) {
                if ($webDriverElement->isDisplayed()) {
                    $elementPerDriver[$browserName] = $webDriverElement;
                    break;
                }
            }
        }
        return new Element($elementPerDriver);
    }

    public function findElements(WebDriverBy $locator): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->elementPerDriver as $browserName => $element) {
            $elementsPerDriver[$browserName] = $element->findElements($locator);
        }
        return new Elements($elementsPerDriver);
    }

    public function isSelected(): bool
    {
        foreach ($this->elementPerDriver as $browserName => $element) {
            if (!isset($value)) {
                $value = $element->isSelected();
            } elseif ($value !== $element->isSelected()) {
                throw new \Exception('Got different values for isSelected');
            }
        }
        return $value;
    }
}
