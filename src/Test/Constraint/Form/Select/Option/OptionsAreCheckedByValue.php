<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class OptionsAreCheckedByValue extends SelectedOptionConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);

        $selectedOptionTexts = $this->getSelectedOptionValues($driver);

        $missingSelections = array_diff($other, $selectedOptionTexts);
        $superfluousSelections = array_diff($selectedOptionTexts, $other);

        return empty($missingSelections) && empty($superfluousSelections);
    }
}
