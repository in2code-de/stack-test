<?php

declare(strict_types=1);

namespace CoStack\StackTest\Elements\Parallel;

use Facebook\WebDriver\WebDriverElement;

class Options
{
    /** @param array<string, array<WebDriverElement>> $options */
    public function __construct(public readonly array $options)
    {
    }
}
