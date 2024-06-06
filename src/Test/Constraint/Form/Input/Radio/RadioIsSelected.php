<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

class RadioIsSelected extends SelectedRadiosConstraint
{
    protected function matches(mixed $other): bool
    {
        $selectedValues = $this->getSelectedOptionValues();
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            return null === $other;
        }
        return $other === $selectedValue;
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        $selectedValues = $this->getSelectedOptionValues();
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            return sprintf(
                'matches radio selection on page %s in browser %s',
                $this->driver->getCurrentURL(),
                $browserName,
            );
        }

        return sprintf(
            'matches selected radio %s on page %s in browser %s',
            $selectedValue,
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
