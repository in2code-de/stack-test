<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Text;

use CoStack\StackTest\Test\Constraint\SessionWithSelectorConstraint;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Util\Exporter;

class FieldNotEquals extends SessionWithSelectorConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $element = $driver->findElement($this->selector);
        return $element->getAttribute('value') !== $other;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'equals not field %s value on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
