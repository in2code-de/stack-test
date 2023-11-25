<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Select\Option;

use CoStack\StackTest\Test\Constraint\Form\SelectableFormConstraint;
use Facebook\WebDriver\Exception\UnexpectedTagNameException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverSelect;
use PHPUnit\Util\Exporter;

abstract class SelectedOptionConstraint extends SelectableFormConstraint
{
    /**
     * @throws UnexpectedTagNameException
     */
    protected function getWebDriverObject(RemoteWebDriver $driver): WebDriverSelect
    {
        $element = $driver->findElement($this->selector);
        return new WebDriverSelect($element);
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
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
