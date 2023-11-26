<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Parallel;

use Facebook\WebDriver\WebDriverElement;

class Option
{
    /** @param array<string, WebDriverElement> $option */
    public function __construct(public readonly array $option)
    {
    }
}
