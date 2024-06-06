<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint\Cookie;

use Exception;
use Facebook\WebDriver\Cookie;
use SebastianBergmann\Comparator\ComparisonFailure;
use SebastianBergmann\Comparator\Factory as ComparatorFactory;

class CookieIsSame extends CookieIsEqual
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
}
