<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverCheckboxes;

class CheckboxesAreNotChecked extends SelectedCheckboxesConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);
        if (is_string($other)) {
            $other = [$other];
        }
        $checkedValues = $this->getCheckedValues($driver);
        return empty(array_intersect($other, $checkedValues));
    }

    /** @return array<RemoteWebElement> */
    protected function getCheckedCheckboxes(WebDriver $driver): array
    {
        $element = $driver->findElement($this->selector);
        $checkboxes = new WebDriverCheckboxes($element);
        return $checkboxes->getAllSelectedOptions();
    }

    protected function getCheckedValues(WebDriver $driver): array
    {
        $values = [];
        foreach ($this->getCheckedCheckboxes($driver) as $checkbox) {
            $values[] = $checkbox->getAttribute('value');
        }
        return $values;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $checkedValues = $this->getCheckedValues($driver);

        return sprintf(
            'is not in the selected checkboxes %s on page %s in browser %s',
            implode(', ', $checkedValues),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
