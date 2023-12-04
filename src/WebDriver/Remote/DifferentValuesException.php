<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use PHPUnit\Framework\AssertionFailedError;
use Throwable;

class DifferentValuesException extends AssertionFailedError
{
    public function __construct(
        public readonly mixed $initial,
        public readonly array $differentValues,
        Throwable $previous = null,
    ) {
        parent::__construct('Got different values for browsers', 1701612767, $previous);
    }
}
