<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements;

interface FormElement
{
    public function getValue(): string|array;

    public function setValue(string|array $value): void;
}
