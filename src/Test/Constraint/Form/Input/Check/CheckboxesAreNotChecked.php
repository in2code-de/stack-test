<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverCheckboxes;

class CheckboxesAreNotChecked extends SelectedCheckboxesConstraint
{
    protected function matches(mixed $other): bool
    {
        $other = $this->resolveSelectorsInOtherToValue($other);
        if (is_string($other)) {
            $other = [$other];
        }
        $checkedValues = $this->getCheckedValues();
        return empty(array_intersect($other, $checkedValues));
    }

    /** @return array<RemoteWebElement> */
    protected function getCheckedCheckboxes(WebDriver $driver): array
    {
        $element = $driver->findElement($this->selector);
        $checkboxes = new WebDriverCheckboxes($element);
        return $checkboxes->getAllSelectedOptions();
    }

    protected function getCheckedValues(): array
    {
        $values = [];
        foreach ($this->getCheckedCheckboxes($this->driver) as $checkbox) {
            $values[] = $checkbox->getAttribute('value');
        }
        return $values;
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        $checkedValues = $this->getCheckedValues();

        return sprintf(
            'is not in the selected checkboxes %s on page %s in browser %s',
            implode(', ', $checkedValues),
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
