<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form;

use CoStack\StackTest\Elements\Form;
use CoStack\StackTest\Test\Constraint\DriverWithSelectorConstraint;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;

class FormDataEquals extends DriverWithSelectorConstraint
{
    protected function matches(mixed $other): bool
    {
        $formData = $this->getFormData();
        try {
            Assert::assertEquals($other, $formData);
        } catch (ExpectationFailedException) {
            return false;
        }
        return true;
    }

    public function toString(bool $exportObjects = false): string
    {
        $formData = $this->getFormData();
        return sprintf('matches actual form data %s', Exporter::export($formData, $exportObjects));
    }

    protected function getFormData(): array
    {
        $form = new Form($this->driver, $this->selector);
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
            $actual = $this->getFormData();
            $diff = new ComparisonFailure(
                $other,
                $actual,
                Exporter::export($other, true),
                Exporter::export($actual, true),
            );
            throw new ExpectationFailedException($exception->getMessage(), $diff);
        }
    }
}
