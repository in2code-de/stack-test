<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverCheckboxes;

class CheckboxesAreNotChecked extends SelectedCheckboxesConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($driver, $other);
        if (is_string($other)) {
            $other = [$other];
        }
        $checkedValues = $this->getCheckedValues($driver);
        return empty(array_intersect($other, $checkedValues));
    }

    /** @return array<RemoteWebElement> */
    protected function getCheckedCheckboxes(RemoteWebDriver $driver): array
    {
        $element = $driver->findElement($this->selector);
        $checkboxes = new WebDriverCheckboxes($element);
        return $checkboxes->getAllSelectedOptions();
    }

    protected function getCheckedValues(RemoteWebDriver $driver): array
    {
        $values = [];
        foreach ($this->getCheckedCheckboxes($driver) as $checkbox) {
            $values[] = $checkbox->getAttribute('value');
        }
        return $values;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
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
