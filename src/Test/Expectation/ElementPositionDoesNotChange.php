<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Expectation;

use Closure;
use CoStack\StackTest\Session\Session;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

use function in_array;

class ElementPositionDoesNotChange
{
    public static function build(WebDriverBy $locator): Closure
    {
        $finishedAnimations = [];
        $previousCoordinates = [];
        return static function (Session|RemoteWebDriver $session) use (
            &$previousCoordinates,
            &$finishedAnimations,
            $locator,
        ): bool {
            $session = Session::elevate($session);
            foreach ($session->drivers as $browserName => $driver) {
                $finishedAnimations[$browserName] ??= false;
                if ($finishedAnimations[$browserName]) {
                    continue;
                }
                $element = $driver->findElement($locator);
                $elementCoordinates = $element->getCoordinates()->onPage();
                $previousElementPosition = $previousCoordinates[$browserName] ?? null;
                if (null !== $previousElementPosition && $elementCoordinates->equals($previousElementPosition)) {
                    $finishedAnimations[$browserName] = true;
                }
                $previousCoordinates[$browserName] = $elementCoordinates;
            }
            return !in_array(false, $finishedAnimations, true);
        };
    }
}
