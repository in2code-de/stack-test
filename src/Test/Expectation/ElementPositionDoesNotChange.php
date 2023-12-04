<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Expectation;

use Closure;
use CoStack\StackTest\WebDriver\Remote\MultiWebDriver;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\WebDriverBy;

use function in_array;

class ElementPositionDoesNotChange
{
    public static function build(WebDriverBy $locator): Closure
    {
        $finished = [];
        $previous = [];
        return static function (WebDriver $driver) use (&$previous, &$finished, $locator): bool {
            $drivers = $driver instanceof MultiWebDriver ? $driver->drivers : [$driver];
            foreach ($drivers as $singleDriver) {
                $finished[$singleDriver->browserName] ??= false;
                if ($finished[$singleDriver->browserName]) {
                    continue;
                }
                $element = $singleDriver->findElement($locator);
                $elementCoordinates = $element->getCoordinates()->onPage();
                $previousElementPosition = $previous[$singleDriver->browserName] ?? null;
                if (null !== $previousElementPosition && $elementCoordinates->equals($previousElementPosition)) {
                    $finished[$singleDriver->browserName] = true;
                }
                $previous[$singleDriver->browserName] = $elementCoordinates;
            }
            return !in_array(false, $finished, true);
        };
    }
}
