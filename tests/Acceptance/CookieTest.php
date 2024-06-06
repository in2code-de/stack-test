<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\WebDriverFactory;
use Facebook\WebDriver\Cookie;
use PHPUnit\Framework\TestCase;

class CookieTest extends TestCase
{
    use DriverAssertions;

    public function testCookieCanBeSetAndUnset(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($driver, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $driver->manage()->addCookie($cookie);

        self::assertCookieIsEqual($driver, $cookie);

        $driver->manage()->deleteCookieNamed($cookie->getName());

        self::assertCookieIsNotSet($driver, $cookie);
    }
}
