<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Elements\AbstractSelectable;
use CoStack\StackTest\Elements\Checkboxes;
use CoStack\StackTest\Elements\FormElement;
use CoStack\StackTest\Elements\Radios;
use CoStack\StackTest\Elements\Select;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCheckboxes;
use Facebook\WebDriver\WebDriverRadios;
use Facebook\WebDriver\WebDriverSelect;

class Session
{
    /** @param array<RemoteWebDriver> $drivers */
    public function __construct(public readonly array $drivers)
    {
    }

    public function get(string $url): void
    {
        foreach ($this->drivers as $driver) {
            $driver->get($url);
        }
    }

    /**
     * Attention! Firefox always sets secure to true, whereas chrome respects the cookie settings.
     */
    public function setCookie(Cookie $cookie): void
    {
        foreach ($this->drivers as $driver) {
            $driver->manage()->addCookie($cookie);
        }
    }

    public function click(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->click();
        }
    }

    public function fillField(WebDriverBy $selector, string $string): void
    {
        foreach ($this->drivers as $driver) {
            $element = $driver->findElement($selector);
            try {
                $element->clear()->sendKeys($string);
            } catch (ElementNotInteractableException $exception) {
                $tagName = $element->getTagName();
                if ('input' === $tagName) {
                    $type = $element->getAttribute('type');
                    $hidden = $element->getAttribute('hidden');
                    if ('hidden' === $type || 'true' === $hidden) {
                        throw new HiddenInputCanNotBeFilledException($element, $exception);
                    }
                }
                throw $exception;
            }
        }
    }

    public function fillHiddenField(WebDriverBy $selector, string $string): void
    {
        $script = <<<JS
function fillHiddenInput(element, value) {
    element.value = value
}
return (fillHiddenInput).apply(null, arguments);
JS;
        foreach ($this->drivers as $driver) {
            $element = $driver->findElement($selector);
            $driver->executeScript($script, [$element, $string]);
        }
    }

    public function clearField(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->clear();
        }
    }

    public function selectOption(WebDriverBy $selector, WebDriverBy|string $option): void
    {
        foreach ($this->drivers as $driver) {
            $selectElement = $driver->findElement($selector);
            $select = new WebDriverSelect($selectElement);
            if (is_string($option)) {
                $select->selectByVisibleText($option);
            } else {
                $option = $selectElement->findElement($option);
                $select->selectByValue($option->getAttribute('value'));
            }
        }
    }

    public function getFormElement(WebDriverBy $selector): FormElement
    {
        $elements = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $element = $driver->findElement($selector);
            $webDriverFormElement = match ($element->getTagName()) {
                'select' => new WebDriverSelect($element),
                'input' => match ($element->getAttribute('type')) {
                    'check' => new WebDriverCheckboxes($element),
                    'radio' => new WebDriverRadios($element),
                },
            };
            $elements[$browserName] = $webDriverFormElement;
        }
        return AbstractSelectable::fromElements($elements);
    }

    public function getCheckboxes(WebDriverBy $selector): Checkboxes
    {
        $checkboxes = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $element = $driver->findElement($selector);
            $checkboxes[$browserName] = new WebDriverCheckboxes($element);
        }
        return new Checkboxes($checkboxes);
    }

    public function getRadios(WebDriverBy $selector): Radios
    {
        $radios = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $element = $driver->findElement($selector);
            $radios[$browserName] = new WebDriverRadios($element);
        }
        return new Radios($radios);
    }

    public function getSelect(WebDriverBy $selector): Select
    {
        $selects = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $element = $driver->findElement($selector);
            $selects[$browserName] = new WebDriverSelect($element);
        }
        return new Select($selects);
    }

    public function submitForm(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->submit();
        }
    }

    public function findElements(WebDriverBy $selector): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->drivers as $driver) {
            $browserName = $driver->getCapabilities()->getBrowserName();
            $elementsPerDriver[$browserName] = $driver->findElements($selector);
        }
        return new Elements($elementsPerDriver);
    }

    public function executeScript(string $javascript, array $arguments = []): void
    {
        foreach ($this->drivers as $driver) {
            $resolvedArguments = $this->resolveWebDriverByForDriver($driver, $arguments);
            $driver->executeScript($javascript, $resolvedArguments);
        }
    }

    public function executeAsyncScript(string $javascript, array $arguments = []): void
    {
        foreach ($this->drivers as $driver) {
            $resolvedArguments = $this->resolveWebDriverByForDriver($driver, $arguments);
            $driver->executeAsyncScript($javascript, $resolvedArguments);
        }
    }

    protected function resolveWebDriverByForDriver(RemoteWebDriver $driver, array $arguments): array
    {
        $resolved = [];
        foreach ($arguments as $index => $argument) {
            if ($argument instanceof WebDriverBy) {
                $argument = $driver->findElement($argument);
            }
            $resolved[$index] = $argument;
        }
        return $resolved;
    }

    public function __destruct()
    {
        foreach ($this->drivers as $driver) {
            $driver->quit();
        }
    }
}
