<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

class IsMultiple
{
    /** @param array<string, bool> */
    public function __construct(public readonly array $isMultiple)
    {
    }

    public function areAllMultiple(): bool
    {
        return !in_array(false, $this->isMultiple, true);
    }

    public function areAllSingle(): bool
    {
        return !in_array(true, $this->isMultiple, true);
    }

    public function areSameInAllBrowsers(): bool
    {
        return $this->areAllSingle() || $this->areAllMultiple();
    }
}
