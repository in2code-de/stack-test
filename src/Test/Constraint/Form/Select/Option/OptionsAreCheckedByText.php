<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use function CoStack\StackTest\resolve;

class OptionsAreCheckedByText extends SelectedOptionConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = resolve($this->driver, $other);

        $selectedOptionTexts = $this->getSelectedOptionTexts();

        $missingSelections = array_diff($other, $selectedOptionTexts);
        $superfluousSelections = array_diff($selectedOptionTexts, $other);

        return empty($missingSelections) && empty($superfluousSelections);
    }
}
