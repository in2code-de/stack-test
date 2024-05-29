<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverPoint;
use Facebook\WebDriver\WebDriverWindow;

class MultiWebDriverWindow extends WebDriverWindow
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(
        public readonly MultiWebDriver $driver,
    ) {}

    public function getPosition(): WebDriverPoint
    {
        return $this->getPositionIfEqual(__FUNCTION__);
    }

    public function getSize(): WebDriverDimension
    {
        return $this->getDimensionIfEqual(__FUNCTION__);
    }

    public function minimize(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->minimize();
        }
        return $this;
    }

    public function maximize(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->maximize();
        }
        return $this;
    }

    public function fullscreen(): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->fullscreen();
        }
        return $this;
    }

    public function setSize(WebDriverDimension $size): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->setSize($size);
        }
        return $this;
    }

    public function setPosition(WebDriverPoint $position): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->setPosition($position);
        }
        return $this;
    }

    public function getScreenOrientation(): string
    {
        return $this->getValueIfEqual(__FUNCTION__);
    }

    public function setScreenOrientation($orientation): static
    {
        foreach ($this->driver->drivers as $driver) {
            $driver->manage()->window()->setScreenOrientation($orientation);
        }
        return $this;
    }

    /**
     * @throws DifferentValuesException
     */
    protected function getPositionIfEqual(string $method, array $arguments = []): WebDriverPoint
    {
        /** @var WebDriverPoint $position */
        $position = $this->driver->getFirstDriver()->manage()->window()->{$method}();
        $differentPositions = [];
        foreach ($this->driver->getDriversExceptFirst() as $driver) {
            /** @var WebDriverPoint $otherPosition */
            $otherPosition = $driver->manage()->window()->{$method}();
            if (!$position->equals($otherPosition)) {
                $differentPositions[$driver->browserName] = $differentPositions;
            }
        }
        if (!empty($differentPositions)) {
            throw new DifferentValuesException($position, $differentPositions);
        }
        return $position;
    }

    /**
     * @throws DifferentValuesException
     */
    protected function getDimensionIfEqual(string $method, array $arguments = []): WebDriverDimension
    {
        /** @var WebDriverDimension $dimension */
        $dimension = $this->driver->getFirstDriver()->manage()->window()->{$method}();
        $differentDimensions = [];
        foreach ($this->driver->getDriversExceptFirst() as $driver) {
            /** @var WebDriverDimension $otherDimension */
            $otherDimension = $driver->manage()->window()->{$method}();
            if (!$dimension->equals($otherDimension)) {
                $differentDimensions[$driver->browserName] = $differentDimensions;
            }
        }
        if (!empty($differentDimensions)) {
            throw new DifferentValuesException($dimension, $differentDimensions);
        }
        return $dimension;
    }

    /**
     * @throws DifferentValuesException
     */
    protected function getValueIfEqual(string $method): mixed
    {
        /** @var string $value */
        $value = $this->driver->getFirstDriver()->manage()->window()->{$method}();
        $differentValues = [];
        foreach ($this->driver->getDriversExceptFirst() as $driver) {
            /** @var string $otherValue */
            $otherValue = $driver->manage()->window()->{$method}();
            if ($value !== $otherValue) {
                $differentValues[$driver->browserName] = $otherValue;
            }
        }
        if (!empty($differentValues)) {
            throw new DifferentValuesException($value, $differentValues);
        }
        return $value;
    }
}
