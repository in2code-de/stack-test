<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Elements\Single\Form;
use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;

class FormDataEquals extends DriverWithSelectorConstraint
{
    protected function driverMatches(mixed $other, WebDriver $driver): bool
    {
        $formData = $this->getFormData($driver);
        try {
            Assert::assertEquals($other, $formData);
        } catch (ExpectationFailedException) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(WebDriver $driver, bool $exportObjects = false): string
    {
        $formData = $this->getFormData($driver);
        return sprintf('matches actual form data %s', Exporter::export($formData, $exportObjects));
    }

    protected function getFormData(WebDriver $driver): array
    {
        $form = new Form($driver, $this->selector);
        return $form->getData();
    }

    protected function fail(mixed $other, string $description, ComparisonFailure $comparisonFailure = null): never
    {
        try {
            parent::fail($other, $description, $comparisonFailure);
        } catch (ExpectationFailedException $exception) {
            if (null !== $exception->getComparisonFailure() || !is_array($other)) {
                throw $exception;
            }
            $diff = null;
            foreach ($this->driver->drivers as $browserName => $driver) {
                if ($this->driverResults[$browserName]) {
                    continue;
                }
                $actual = $this->getFormData($driver);
                $diff = new ComparisonFailure(
                    $other,
                    $actual,
                    Exporter::export($other, true),
                    Exporter::export($actual, true),
                );
                break;
            }
            throw new ExpectationFailedException($exception->getMessage(), $diff);
        }
    }
}
