<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class CheckboxesAreChecked extends SelectedCheckboxesConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);
        if (is_string($other)) {
            $other = [$other];
        }

        $checkedValues = $this->getSelectedOptionValues($driver);

        $superfluousSelections = array_diff($other, $checkedValues);
        $missingSelections = array_diff($checkedValues, $other);

        return empty($superfluousSelections) && empty($missingSelections);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $checkedValues = $this->getSelectedOptionValues($driver);

        return sprintf(
            'matches selected checkboxes %s on page %s in browser %s',
            implode(', ', $checkedValues),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
