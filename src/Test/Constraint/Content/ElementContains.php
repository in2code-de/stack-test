<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Content;

use CoStack\StackTest\Session;
use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Util\Exporter;

class ElementContains extends SessionConstrain
{
    public function __construct(
        RemoteWebDriver|Session $session,
        protected readonly WebDriverBy $selector,
    ) {
        parent::__construct($session);
    }

    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        $elements = $driver->findElements($this->selector);
        foreach ($elements as $element) {
            $text = $element->getText();
            if (str_contains($text, $other)) {
                return true;
            }
        }
        return false;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();

        return sprintf(
            'is visible in element %s on page %s in browser %s',
            Exporter::export($this->selector, $exportObjects),
            $driver->getCurrentURL(),
            $browserName,
        );
    }
}
