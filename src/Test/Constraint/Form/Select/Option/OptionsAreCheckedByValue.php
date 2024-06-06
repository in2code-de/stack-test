<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

class OptionsAreCheckedByValue extends SelectedOptionConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($other);

        $selectedOptionTexts = $this->getSelectedOptionValues();

        $missingSelections = array_diff($other, $selectedOptionTexts);
        $superfluousSelections = array_diff($selectedOptionTexts, $other);

        return empty($missingSelections) && empty($superfluousSelections);
    }
}
