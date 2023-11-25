<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class CheckboxIsChecked extends SessionConstrain
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $element = $driver->findElement($other);
        return 'true' === $element->getAttribute('checked');
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is checked on page %s in browser %s',
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
