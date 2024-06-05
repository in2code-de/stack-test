<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Multiple;

use CoStack\StackTest\Elements\Single\Checkboxes as SingleCheckboxes;
use CoStack\StackTest\WebDriver\Remote\DifferentValuesException;
use Facebook\WebDriver\Remote\RemoteWebElement;

use function current;
use function reset;

class Checkboxes
{
    /** @var array<SingleCheckboxes> */
    public readonly array $elements;

    /**
     * @param array<RemoteWebElement> $elements
     */
    public function __construct(
        array $elements,
    ) {
        $converted = [];
        foreach ($elements as $element) {
            $converted[] = new SingleCheckboxes($element);
        }
        $this->elements = $converted;
    }

    public function isMultiple(): bool
    {
        $firstElement = reset($this->elements);
        $initial = $firstElement->isMultiple();
        while (next($this->elements)) {
            $next = current($this->elements);
            if ($next->isMultiple() !== $initial) {
                throw new DifferentValuesException($initial, $next->isMultiple());
            }
        }
        return $initial;
    }

    public function deselectAll(): void
    {
        foreach ($this->elements as $element) {
            $element->deselectAll();
        }
    }

    public function deselectByIndex($index): void
    {
        foreach ($this->elements as $element) {
            $element->deselectByIndex($index);
        }
    }

    public function deselectByValue($value): void
    {
        foreach ($this->elements as $element) {
            $element->deselectByValue($value);
        }
    }

    public function deselectByVisibleText($text): void
    {
        foreach ($this->elements as $element) {
            $element->deselectByVisibleText($text);
        }
    }

    public function deselectByVisiblePartialText($text): void
    {
        foreach ($this->elements as $element) {
            $element->deselectByVisiblePartialText($text);
        }
    }

    public function selectAll(): void
    {
        foreach ($this->elements as $element) {
            $element->selectAll();
        }
    }

    public function selectByIndex($index)
    {
        foreach ($this->elements as $element) {
            $element->selectByIndex($index);
        }
    }

    public function selectByValue($value): void
    {
        foreach ($this->elements as $element) {
            $element->selectByValue($value);
        }
    }

    public function selectByVisibleText($text): void
    {
        foreach ($this->elements as $element) {
            $element->selectByVisibleText($text);
        }
    }

    public function selectByVisiblePartialText($text): void
    {
        foreach ($this->elements as $element) {
            $element->selectByVisiblePartialText($text);
        }
    }
}
