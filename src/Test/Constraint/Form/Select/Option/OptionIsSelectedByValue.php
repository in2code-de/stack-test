<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use Facebook\WebDriver\Remote\RemoteWebDriver;

class OptionIsSelectedByValue extends SelectedOptionConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);
        if (!is_string($other)) {
            throw new Exception(
                'Value to compare the selected option\'s value to must be string but is ' . get_debug_type($other),
            );
        }

        $selectedOptionValues = $this->getSelectedOptionValues($driver);
        $selectedOptionValue = reset($selectedOptionValues);
        if (!is_string($selectedOptionValue)) {
            return false;
        }

        return $other === $selectedOptionValue;
    }
}
