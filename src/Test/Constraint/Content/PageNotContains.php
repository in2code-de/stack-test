<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Facebook\WebDriver\WebDriverBy;

class PageNotContains extends DriverConstrain
{
    protected function matches(mixed $other): bool
    {
        $selector = WebDriverBy::cssSelector('body');
        $remoteWebElement = $this->driver->findElement($selector);
        $text = $remoteWebElement->getText();
        return !str_contains($text, $other);
    }

    public function toString(bool $exportObjects = false): string
    {
        return sprintf(
            'is not displayed on page %s in %s',
            $this->driver->getCurrentURL(),
            $this->driver->getCapabilities()->getBrowserName(),
        );
    }
}
