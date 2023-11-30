<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session\Session;
use Facebook\WebDriver\WebDriverBy;

class IframeNavigationTest extends BrowserTestCase
{
    public function testSwitchToIframeAndBack(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/iframe.php');

        self::assertPageContains($session, 'Main frame');
        self::assertPageNotContains($session, 'Eye frame');
        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/iframe.php');

        $session->inIFrameContext(WebDriverBy::id('content-iframe'), function (Session $session): void {
            self::assertPageContains($session, 'Eye frame');
            self::assertPageNotContains($session, 'Main Frame');
            self::assertPageNotContains($session, 'Eye frame two');
            $session->click(WebDriverBy::linkText('content 2'));
            self::assertPageContains($session, 'Eye frame two');
        });

        self::assertPageContains($session, 'Main frame');
        self::assertPageNotContains($session, 'Eye frame');
        self::assertPageNotContains($session, 'Eye frame two');
    }
}
