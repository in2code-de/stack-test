<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Elements\Single\Form;
use CoStack\StackTest\Test\Constraint\SessionWithSelectorConstraint;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;

class FormDataEquals extends SessionWithSelectorConstraint
{
    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $formData = $this->getFormData($driver);
        try {
            Assert::assertEquals($other, $formData);
        } catch (ExpectationFailedException) {
            return false;
        }
        return true;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $formData = $this->getFormData($driver);
        return sprintf('matches actual form data %s', Exporter::export($formData, $exportObjects));
    }

    protected function getFormData(RemoteWebDriver $driver): array
    {
        $formElement = $driver->findElement($this->selector);
        $form = new Form($formElement);
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
            foreach ($this->session->drivers as $browserName => $driver) {
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
