<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use Exception;

use function CoStack\StackTest\resolve;

class OptionIsSelectedByText extends SelectedOptionConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = resolve($this->driver, $other);
        if (!is_string($other)) {
            throw new Exception(
                'Value to compare the selected option\'s text to must be string but is ' . get_debug_type($other),
            );
        }

        $selectedOptionTexts = $this->getSelectedOptionTexts();
        $selectedOptionText = reset($selectedOptionTexts);
        if (!is_string($selectedOptionText)) {
            return false;
        }

        return $other === $selectedOptionText;
    }
}
