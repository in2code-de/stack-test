<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Expectation;

use Closure;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverPoint;

class ElementPositionDoesNotChange
{
    public static function build(WebDriverBy $locator): Closure
    {
        /** @var list<WebDriverPoint> $previous */
        $previous = [];
        return static function (WebDriver $driver) use (&$previous, $locator): bool {
            $elements = $driver->findElements($locator);
            if (empty($elements)){
                return true;
            }
            foreach ($elements as $element) {
                $elementId = $element->getID();
                try {
                    $elementCoordinates = $element->getCoordinates()->onPage();
                } catch (StaleElementReferenceException) {
                    continue;
                }
                $previousElementPosition = $previous[$elementId] ?? null;
                if (
                    null !== $previousElementPosition
                    // Workaround for https://github.com/php-webdriver/php-webdriver/issues/1086
                    && $elementCoordinates->getX() === $previousElementPosition->getX()
                    && $elementCoordinates->getY() === $previousElementPosition->getY()
                ) {
                    return true;
                }
                $previous[$elementId] = $elementCoordinates;
            }
            return false;
        };
    }
}
