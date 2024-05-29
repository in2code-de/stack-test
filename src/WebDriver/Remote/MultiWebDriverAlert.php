<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Facebook\WebDriver\WebDriverAlert;

class MultiWebDriverAlert extends WebDriverAlert
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        public readonly MultiWebDriver $multiWebDriver,
    ) {}

    public function accept(): static
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->alert()->accept();
        }
        return $this;
    }

    public function dismiss(): static
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->alert()->dismiss();
        }
        return $this;
    }

    public function getText(): string
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function sendKeys($value): static
    {
        foreach ($this->multiWebDriver->drivers as $driver) {
            $driver->switchTo()->alert()->sendKeys($value);
        }
        return $this;
    }

    /**
     * @throws DifferentValuesException
     */
    protected function getReturnValue(string $method): mixed
    {
        $value = $this->multiWebDriver->getFirstDriver()->switchTo()->alert()->{$method}();
        $otherValues = [];
        foreach ($this->multiWebDriver->getDriversExceptFirst() as $driver) {
            $otherValue = $this->multiWebDriver->getFirstDriver()->switchTo()->alert()->{$method}();
            if ($value !== $otherValue) {
                $otherValues[$driver->browserName] = $otherValue;
            }
        }
        if (!empty($otherValues)) {
            throw new DifferentValuesException($value, $otherValues);
        }
        return $value;
    }
}
