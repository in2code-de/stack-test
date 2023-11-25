<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use Exception;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class RadioIsNotSelected extends SelectedRadiosConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);
        if (!is_string($other) && null !== $other) {
            throw new Exception(
                'Value to test against must be of type string or null ' . get_debug_type($other) . ' given',
            );
        }

        $selectedValues = $this->getSelectedOptionValues($driver);
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            // No radio selected, check if selection is not "no selection" (=null)
            return null !== $other;
        }

        return $other !== $selectedValues;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $selectedValue = $this->getSelectedValue($driver);

        return sprintf(
            'does not match selected radio %s on page %s in browser %s',
            $selectedValue,
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
