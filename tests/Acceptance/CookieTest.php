<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\Factory;
use Facebook\WebDriver\Cookie;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    use DriverAssertions;

    public function testCookieCanBeSetAndUnset(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($driver, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $driver->manage()->addCookie($cookie);

        self::assertCookieIsEqual($driver, $cookie);

        $driver->manage()->deleteCookieNamed($cookie->getName());

        self::assertCookieIsNotSet($driver, $cookie);
    }

    public function testCookieIsRemovedBySessionReset(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($driver, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $driver->manage()->addCookie($cookie);

        self::assertCookieIsEqual($driver, $cookie);

        $driver->reset();

        self::assertCookieIsNotSet($driver, $cookie);
    }
}
