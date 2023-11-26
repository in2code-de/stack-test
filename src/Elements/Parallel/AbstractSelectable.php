<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Parallel;

use Exception;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\WebDriverCheckboxes;
use Facebook\WebDriver\WebDriverRadios;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverSelectInterface;

abstract class AbstractSelectable implements WebDriverSelectInterface, FormElement
{
    /** @param array<string, WebDriverSelectInterface> $elements */
    public function __construct(public readonly array $elements)
    {
    }

    public static function fromElements(array $elements): FormElement
    {
        $type = null;
        foreach ($elements as $element) {
            $detectedType = get_class($element);
            if (null === $type) {
                $type = $detectedType;
            } elseif ($type !== $detectedType) {
                throw new Exception(sprintf('Form elements have different types: %s <> %s', $type, $detectedType));
            }
        }
        return match ($type) {
            WebDriverSelect::class => new Select($elements),
            WebDriverRadios::class => new Radios($elements),
            WebDriverCheckboxes::class => new Checkboxes($elements),
            default => throw new Exception('could not determine FormElement by WebDriverType ' . $type),
        };
    }

    public function getSelectedValues(): array
    {
        $isMultiple = $this->isMultiple();
        if (!$isMultiple->areSameInAllBrowsers()) {
            throw new Exception('Selectables are different in browsers, can not return selected values');
        }

        if ($isMultiple->areAllSingle()) {
            $values = [];
            foreach ($this->elements as $browserName => $element) {
                try {
                    $selectedOption = $element->getFirstSelectedOption();
                    $values[$browserName] = $selectedOption->getAttribute('value');
                } catch (NoSuchElementException) {
                }
            }
            return $values;
        }

        $values = [];
        foreach ($this->elements as $browserName => $element) {
            try {
                $selectedOptions = $element->getAllSelectedOptions();
                foreach ($selectedOptions as $selectedOption) {
                    $values[$browserName][] = $selectedOption->getAttribute('value');
                }
            } catch (NoSuchElementException) {
            }
        }
        return $values;
    }

    public function getSelectedTexts(): array
    {
        $isMultiple = $this->isMultiple();
        if (!$isMultiple->areSameInAllBrowsers()) {
            throw new Exception('Selectables are different in browsers, can not return selected values');
        }

        if ($isMultiple->areAllSingle()) {
            $values = [];
            foreach ($this->elements as $browserName => $element) {
                try {
                    $selectedOption = $element->getFirstSelectedOption();
                    $values[$browserName] = $selectedOption->getText();
                } catch (NoSuchElementException) {
                }
            }
            return $values;
        }

        $values = [];
        foreach ($this->elements as $browserName => $element) {
            try {
                $selectedOptions = $element->getAllSelectedOptions();
                foreach ($selectedOptions as $selectedOption) {
                    $values[$browserName][] = $selectedOption->getText();
                }
            } catch (NoSuchElementException) {
            }
        }
        return $values;
    }

    public function isMultiple(): IsMultiple
    {
        $isMultiple = [];
        foreach ($this->elements as $browserName => $element) {
            $isMultiple[$browserName] = $element->isMultiple();
        }
        return new IsMultiple($isMultiple);
    }

    public function getOptions(): Options
    {
        $options = [];
        foreach ($this->elements as $browserName => $element) {
            $options[$browserName] = $element->getOptions();
        }
        return new Options($options);
    }

    public function getAllSelectedOptions(): Options
    {
        $options = [];
        foreach ($this->elements as $browserName => $element) {
            $options[$browserName] = $element->getAllSelectedOptions();
        }
        return new Options($options);
    }

    public function getFirstSelectedOption(): Option
    {
        $option = [];
        foreach ($this->elements as $browserName => $element) {
            $option[$browserName] = $element->getFirstSelectedOption();
        }
        return new Option($option);
    }

    public function selectByIndex($index): static
    {
        foreach ($this->elements as $element) {
            $element->selectByIndex($index);
        }
        return $this;
    }

    public function selectByValue($value): static
    {
        foreach ($this->elements as $element) {
            $element->selectByValue($value);
        }
        return $this;
    }

    public function selectByVisibleText($text): static
    {
        foreach ($this->elements as $element) {
            $element->selectByVisibleText($text);
        }
        return $this;
    }

    public function selectByVisiblePartialText($text): static
    {
        foreach ($this->elements as $element) {
            $element->selectByVisiblePartialText($text);
        }
        return $this;
    }

    public function deselectAll(): static
    {
        foreach ($this->elements as $element) {
            $element->deselectAll();
        }
        return $this;
    }

    public function deselectByIndex($index): static
    {
        foreach ($this->elements as $element) {
            $element->deselectByIndex($index);
        }
        return $this;
    }

    public function deselectByValue($value): static
    {
        foreach ($this->elements as $element) {
            $element->deselectByValue($value);
        }
        return $this;
    }

    public function deselectByVisibleText($text): static
    {
        foreach ($this->elements as $element) {
            $element->deselectByVisibleText($text);
        }
        return $this;
    }

    public function deselectByVisiblePartialText($text): static
    {
        foreach ($this->elements as $element) {
            $element->deselectByVisiblePartialText($text);
        }
        return $this;
    }
}
