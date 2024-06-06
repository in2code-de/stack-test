<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use Exception;

use function reset;

class RadioIsNotSelected extends SelectedRadiosConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($other);
        if (!is_string($other) && null !== $other) {
            throw new Exception(
                'Value to test against must be of type string or null ' . get_debug_type($other) . ' given',
            );
        }

        $selectedValues = $this->getSelectedOptionValues();
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            // No radio selected, check if selection is not "no selection" (=null)
            return null !== $other;
        }

        return $other !== $selectedValues;
    }

    public function toString(bool $exportObjects = false): string
    {
        $selectedValues = $this->getSelectedOptionValues();
        $selectedValue = reset($selectedValues);

        return sprintf(
            'does not match selected radio %s on page %s in browser %s',
            $selectedValue,
            $this->driver->getCurrentURL(),
            $this->driver->browserName,
        );
    }
}
