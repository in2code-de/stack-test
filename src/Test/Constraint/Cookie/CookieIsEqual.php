<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use CoStack\StackTest\Test\Constraint\DriverConstrain;
use Exception;
use Facebook\WebDriver\Cookie;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

class CookieIsEqual extends DriverConstrain
{
    protected ?array $setCookie = null;

    protected function matches(mixed $other): bool
    {
        if (!$other instanceof Cookie) {
            throw new Exception('Cookie must be instance of \Facebook\WebDriver\Cookie');
        }
        $cookies = $this->driver->manage()->getCookies();
        foreach ($cookies as $cookie) {
            if ($cookie->getName() === $other->getName()) {
                $browserName = $this->driver->getCapabilities()->getBrowserName();
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

    public function toString(bool $exportObjects = false): string
    {
        $browserName = $this->driver->getCapabilities()->getBrowserName();
        $lastVisitedUrl = $this->driver->getCurrentURL();

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
