<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use CoStack\StackTest\Test\Constraint\SessionConstrain;
use Exception;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

class SetCookieIsEqual extends SessionConstrain
{
    protected ?array $setCookie = null;

    protected function driverMatches(mixed $other, RemoteWebDriver $driver): bool
    {
        if (!$other instanceof Cookie) {
            throw new Exception('Cookie must be instance of \Facebook\WebDriver\Cookie');
        }
        $cookies = $driver->manage()->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $other->getName()) {
                $browserName = $driver->getCapabilities()->getBrowserName();
                $this->setCookie[$browserName] = $cookie;

                $cookieArray = $cookie->toArray();
                foreach (array_keys($cookieArray) as $name) {
                    if (!$other->offsetExists($name)) {
                        $cookie->offsetUnset($name);
                    }
                }

                $comparatorFactory = ComparatorFactory::getInstance();
                $comparator = $comparatorFactory->getComparatorFor($other, $cookie);
                try {
                    $comparator->assertEquals($cookie, $other);
                } catch (ComparisonFailure) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    protected function descriptionForDriver(RemoteWebDriver $driver, bool $exportObjects = false): string
    {
        $browserName = $driver->getCapabilities()->getBrowserName();
        $lastVisitedUrl = $driver->getCurrentURL();

        if (!isset($this->setCookie[$browserName])) {
            return sprintf(
                'is set on page %s in browser %s',
                $lastVisitedUrl,
                $browserName,
            );
        }
        if (empty($this->setCookie[$browserName])) {
            return sprintf('matches on page %s in browser %s', $lastVisitedUrl, $browserName);
        }
        return sprintf(
            'equals %s on page %s in %s',
            Exporter::export($this->setCookie[$browserName], $exportObjects),
            $lastVisitedUrl,
            $browserName,
        );
    }
}
