<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use Exception;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;

use function is_string;

function resolveElementText(WebDriverElement $element): string
{
    return match ($element->getTagName()) {
        'textarea', 'input' => $element->getAttribute('value'),
        'select' => throw new Exception('TODO implementation'),
        default => $element->getText(),
    };
}

/**
 * @param WebDriver $driver
 * @param WebDriverBy|array<WebDriverBy>|string $by
 * @return WebDriverElement|array<WebDriverElement>
 */
function resolve(WebDriver $driver, WebDriverBy|array|string $by): WebDriverElement|array|string
{
    if ($by instanceof WebDriverBy) {
        return $driver->findElement($by);
    }
    if (is_string($by)) {
        return $by;
    }
    foreach ($by as $index => $bye) {
        $by[$index] = resolve($driver, $bye);
    }
    return $by;
}
