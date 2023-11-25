<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\SessionWithSelectorConstraint;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Util\Exporter;

class ElementNotContains extends SessionWithSelectorConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $elements = $driver->findElements($this->selector);
        foreach ($elements as $element) {
            $value = match ($element->getTagName()) {
                'input' => $element->getAttribute('value'),
                default => $element->getText(),
            };
            if (str_contains($value, $other)) {
                return false;
            }
        }
        return true;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'constrained by %s is not visible on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
