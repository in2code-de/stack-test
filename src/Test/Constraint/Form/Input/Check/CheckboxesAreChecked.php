<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

class CheckboxesAreChecked extends SelectedCheckboxesConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($other);
        if (is_string($other)) {
            $other = [$other];
        }

        $checkedValues = $this->getSelectedOptionValues();

        $superfluousSelections = array_diff($other, $checkedValues);
        $missingSelections = array_diff($checkedValues, $other);

        return empty($superfluousSelections) && empty($missingSelections);
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        $checkedValues = $this->getSelectedOptionValues();

        return sprintf(
            'matches selected checkboxes %s on page %s in browser %s',
            implode(', ', $checkedValues),
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
