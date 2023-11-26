<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Single;

interface FormElement
{
    public function getValue(): string|array;

    public function setValue(string|array $value): void;
}
