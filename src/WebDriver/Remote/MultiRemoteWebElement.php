<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Closure;
use Exception;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Remote\FileDetector;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverPoint;
use Generator;

class MultiRemoteWebElement extends RemoteWebElement
{
    /**
     * @param array<RemoteWebElement> $elements
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        public readonly array $elements,
    ) {}

    public function yieldElements(): Generator
    {
        return yield from $this->elements;
    }

    public function clear(): static
    {
        foreach ($this->elements as $element) {
            $element->clear();
        }
        return $this;
    }

    public function click(): static
    {
        foreach ($this->elements as $element) {
            $element->click();
        }
        return $this;
    }

    public function findElement(WebDriverBy $by): MultiRemoteWebElement
    {
        $result = [];
        foreach ($this->elements as $element) {
            $result[] = $element->findElement($by);
        }
        return new MultiRemoteWebElement($result);
    }

    /** @return array<MultiRemoteWebElement> */
    public function findElements(WebDriverBy $by): array
    {
        $results = [];
        foreach ($this->elements as $outerIndex => $element) {
            $elements = $element->findElements($by);
            foreach ($elements as $innerIndex => $result) {
                $results[$innerIndex][$outerIndex] = $result;
            }
        }
        foreach ($results as $index => $elements) {
            $results[$index] = new MultiRemoteWebElement($elements);
        }
        return $results;
    }

    protected function getValueIfEqual(Closure $getter): mixed
    {
        $elements = $this->yieldElements();
        $elements->rewind();
        $first = $elements->current();
        $firstValue = $getter($first);
        $otherValues = [];
        foreach ($elements as $element) {
            $otherValue = $getter($element);
            if ($firstValue !== $otherValue) {
                $otherValues[] = $otherValue;
            }
        }
        if (!empty($otherValues)) {
            throw new DifferentValuesException($firstValue, $otherValues);
        }
        return $firstValue;
    }

    protected function getPointIfEqual(Closure $getter): WebDriverPoint
    {
        $elements = $this->yieldElements();
        $elements->rewind();
        $first = $elements->current();
        /** @var WebDriverPoint $firstValue */
        $firstValue = $getter($first);
        $otherValues = [];
        foreach ($elements as $element) {
            /** @var WebDriverPoint $otherValue */
            $otherValue = $getter($element);
            if (!$firstValue->equals($otherValue)) {
                $otherValues[] = $otherValue;
            }
        }
        if (!empty($otherValues)) {
            throw new DifferentValuesException($firstValue, $otherValues);
        }
        return $firstValue;
    }

    protected function getCoordinatesIfEqual(Closure $getter): WebDriverCoordinates
    {
        $elements = $this->yieldElements();
        $elements->rewind();
        $first = $elements->current();
        /** @var WebDriverCoordinates $firstValue */
        $firstValue = $getter($first);
        $otherValues = [];
        foreach ($elements as $element) {
            /** @var WebDriverCoordinates $otherValue */
            $otherValue = $getter($element);
            if (!$firstValue->inViewPort()->equals($otherValue->inViewPort())) {
                $otherValues[] = $otherValue;
            }
        }
        if (!empty($otherValues)) {
            throw new DifferentValuesException($firstValue, $otherValues);
        }
        return $firstValue;
    }

    protected function getDimensionsIfEqual(Closure $getter): WebDriverDimension
    {
        $elements = $this->yieldElements();
        $elements->rewind();
        $first = $elements->current();
        /** @var WebDriverDimension $firstValue */
        $firstValue = $getter($first);
        $otherValues = [];
        foreach ($elements as $element) {
            /** @var WebDriverDimension $otherValue */
            $otherValue = $getter($element);
            if (!$firstValue->equals($otherValue)) {
                $otherValues[] = $otherValue;
            }
        }
        if (!empty($otherValues)) {
            throw new DifferentValuesException($firstValue, $otherValues);
        }
        return $firstValue;
    }

    public function getAttribute($attribute_name): string|bool|null
    {
        $getter = static fn(RemoteWebElement $element): string|bool|null => $element->getAttribute($attribute_name);

        return $this->getValueIfEqual($getter);
    }

    public function getDomProperty($propertyName): mixed
    {
        $getter = static fn(RemoteWebElement $element): mixed => $element->getDomProperty($propertyName);

        return $this->getValueIfEqual($getter);
    }

    public function getCSSValue($css_property_name): string
    {
        $getter = static fn(RemoteWebElement $element): string => $element->getCSSValue($css_property_name);

        return $this->getValueIfEqual($getter);
    }

    public function getLocation(): WebDriverPoint
    {
        $getter = static fn(RemoteWebElement $element): WebDriverPoint => $element->getLocation();

        return $this->getPointIfEqual($getter);
    }

    public function getLocationOnScreenOnceScrolledIntoView(): WebDriverPoint
    {
        $getter = static fn(RemoteWebElement $element): WebDriverPoint => $element->getLocationOnScreenOnceScrolledIntoView();

        return $this->getPointIfEqual($getter);
    }

    public function getCoordinates(): WebDriverCoordinates
    {
        $getter = static fn(RemoteWebElement $element): WebDriverCoordinates => $element->getCoordinates();

        return $this->getCoordinatesIfEqual($getter);
    }

    public function getSize(): WebDriverDimension
    {
        $getter = static fn(RemoteWebElement $element): WebDriverDimension => $element->getSize();

        return $this->getDimensionsIfEqual($getter);
    }

    public function getTagName(): string
    {
        $getter = static fn(RemoteWebElement $element): string => $element->getTagName();

        return $this->getValueIfEqual($getter);
    }

    public function getText(): string
    {
        $getter = static fn(RemoteWebElement $element): string => $element->getText();

        return $this->getValueIfEqual($getter);
    }

    public function isDisplayed(): bool
    {
        $getter = static fn(RemoteWebElement $element): bool => $element->isDisplayed();

        return $this->getValueIfEqual($getter);
    }

    public function isEnabled(): bool
    {
        $getter = static fn(RemoteWebElement $element): bool => $element->isEnabled();

        return $this->getValueIfEqual($getter);
    }

    public function isSelected(): bool
    {
        $getter = static fn(RemoteWebElement $element): bool => $element->isSelected();

        return $this->getValueIfEqual($getter);
    }

    public function sendKeys($value): static
    {
        foreach ($this->elements as $element) {
            $element->sendKeys($value);
        }
        return $this;
    }

    public function setFileDetector(FileDetector $detector): static
    {
        foreach ($this->elements as $element) {
            $element->setFileDetector($detector);
        }
        return $this;
    }

    public function submit(): static
    {
        foreach ($this->elements as $element) {
            $element->submit();
        }
        return $this;
    }

    public function getID(): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachID instead.',
        );
    }

    public function foreachID(Closure $callback): void
    {
        foreach ($this->elements as $element) {
            $callback($element->getID());
        }
    }

    public function takeElementScreenshot($save_as = null)
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachElementScreenshot instead.',
        );
    }

    public function foreachElementScreenshot(Closure $callback, $save_as = null): void
    {
        foreach ($this->elements as $element) {
            $callback($element->foreachElementScreenshot($save_as));
        }
    }

    public function equals(WebDriverElement $other): bool
    {
        if ($other instanceof MultiRemoteWebElement) {
            foreach ($other->elements as $index => $element) {
                if (!isset($this->elements[$index])) {
                    return false;
                }
                if (!$this->elements[$index]->equals($element)) {
                    return false;
                }
            }
            return true;
        }
        foreach ($this->elements as $element) {
            if (!$element->equals($other)) {
                return false;
            }
        }
        return true;
    }

    public function getShadowRoot(): MultiShadowRoot
    {
        $elements = [];
        foreach ($this->elements as $element) {
            $elements[] = $element->getShadowRoot();
        }
        return new MultiShadowRoot($elements);
    }

    protected function clickChildElement(ElementNotInteractableException $originalException): void
    {
        parent::clickChildElement($originalException);
    }

    protected function newElement($id): RemoteWebElement
    {
        return parent::newElement($id);
    }

    protected function upload($local_file): string
    {
        return parent::upload($local_file);
    }

    protected function createTemporaryZipArchive($fileToZip): string
    {
        return parent::createTemporaryZipArchive($fileToZip);
    }
}
