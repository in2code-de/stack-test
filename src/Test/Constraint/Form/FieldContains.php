<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Session;
use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Util\Exporter;

class FieldContains extends SessionConstrain
{
    public function __construct(
        RemoteWebDriver|Session $session,
        protected readonly WebDriverBy $selector,
    ) {
        parent::__construct($session);
    }
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $element = $driver->findElement($this->selector);
        return str_contains($element->getAttribute('value'), $other);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is in field %s value on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
