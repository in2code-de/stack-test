<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\Cookie;

class CookieTest extends BrowserTestCase
{
    protected Session $session;

    protected function setUp(): void
    {
        $this->session = SessionFactory::getInstance()->create('session1');
    }

    protected function tearDown(): void
    {
        $this->session->reset();
    }

    public function testCookieCanBeSetAndUnset(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($session, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $session->setCookie($cookie);

        self::assertCookieIsEqual($session, $cookie);

        $session->deleteCookie($cookie);

        self::assertCookieIsNotSet($session, $cookie);
    }

    public function testCookieIsRemovedBySessionReset(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($session, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $session->setCookie($cookie);

        self::assertCookieIsEqual($session, $cookie);

        $session->reset();

        self::assertCookieIsNotSet($session, $cookie);
    }
}
