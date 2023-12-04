<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Exception;

class RadioIsNotSelected extends SelectedRadiosConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
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

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $selectedValue = $this->getSelectedValue($driver);

        return sprintf(
            'does not match selected radio %s on page %s in browser %s',
            $selectedValue,
            $driver->getCurrentURL(),
            $driver->browserName,
        );
    }
}
