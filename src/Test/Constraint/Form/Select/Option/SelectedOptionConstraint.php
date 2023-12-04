<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use CoStack\StackTest\Test\Constraint\Form\SelectableFormConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\WebDriverSelect;
use PHPUnit\Util\Exporter;

abstract class SelectedOptionConstraint extends SelectableFormConstraint
{
    /**
     * @throws UnexpectedTagNameException
     */
    protected function getWebDriverObject(WebDriver $driver): WebDriverSelect
    {
        $element = $driver->findElement($this->selector);
        return new WebDriverSelect($element);
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        $selectedValuesString = [];
        foreach ($this->getWebDriverObject($driver) as $selectedOption) {
            $selectedValuesString[] = $selectedOption->getText() . '(' . $selectedOption->getAttribute('value') . ')';
        }
        if (empty($selectedValuesString)) {
            $selectedValuesString = ['(no selection)'];
        }

        return sprintf(
            'is selected in field %s with selected elements %s on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            implode(', ', $selectedValuesString),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
