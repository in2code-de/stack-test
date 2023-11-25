<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use Facebook\WebDriver\Remote\RemoteWebDriver;

class OptionsAreCheckedByText extends SelectedOptionConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToText($driver, $other);

        $selectedOptionTexts = $this->getSelectedOptionTexts($driver);

        $missingSelections = array_diff($other, $selectedOptionTexts);
        $superfluousSelections = array_diff($selectedOptionTexts, $other);

        return empty($missingSelections) && empty($superfluousSelections);
    }
}
