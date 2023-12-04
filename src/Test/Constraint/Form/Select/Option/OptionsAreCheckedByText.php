<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

use function CoStack\StackTest\resolve;

class OptionsAreCheckedByText extends SelectedOptionConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $other = resolve($driver, $other);

        $selectedOptionTexts = $this->getSelectedOptionTexts($driver);

        $missingSelections = array_diff($other, $selectedOptionTexts);
        $superfluousSelections = array_diff($selectedOptionTexts, $other);

        return empty($missingSelections) && empty($superfluousSelections);
    }
}
