<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Radio;

use CoStack\StackTest\WebDriver\Remote\WebDriver;

class RadioIsSelected extends SelectedRadiosConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $selectedValues = $this->getSelectedOptionValues($driver);
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            return null === $other;
        }
        return $other === $selectedValue;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $selectedValues = $this->getSelectedOptionValues($driver);
        $selectedValue = reset($selectedValues);
        if (!is_string($selectedValue)) {
            return sprintf(
                'matches radio selection on page %s in browser %s',
                $driver->getCurrentURL(),
                $browserName,
            );
        }

        return sprintf(
            'matches selected radio %s on page %s in browser %s',
            $selectedValue,
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
