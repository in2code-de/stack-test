<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Form\Input\Check;

use CoStack\StackTest\Test\Constraint\DriverConstrain;

class CheckboxIsChecked extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $element = $this->driver->findElement($other);
        return 'true' === $element->getAttribute('checked');
    }

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is checked on page %s in browser %s',
            $this->driver->getCurrentURL(),
            $browserName,
        );
    }
}
