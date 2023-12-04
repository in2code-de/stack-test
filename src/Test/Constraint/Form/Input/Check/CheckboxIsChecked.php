<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use CoStack\StackTest\WebDriver\Remote\WebDriver;

class CheckboxIsChecked extends DriverConstrain
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $element = $driver->findElement($other);
        return 'true' === $element->getAttribute('checked');
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is checked on page %s in browser %s',
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
