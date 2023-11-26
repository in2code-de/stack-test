<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class IframeNavigationTest extends BrowserTestCase
{
    public function testSwitchToIframeAndBack(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/iframe.php');

        self::assertPageContains($session, 'Main frame');
        self::assertPageNotContains($session, 'Eye frame');
        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/iframe.php');

        $session->inIFrameContext(WebDriverBy::id('content-iframe'), function (RemoteWebDriver $driver): void {
            self::assertPageContains($driver, 'Eye frame');
            self::assertPageNotContains($driver, 'Main Frame');
            self::assertPageNotContains($driver, 'Eye frame two');
            $driver->findElement(WebDriverBy::linkText('content 2'))->click();
            self::assertPageContains($driver, 'Eye frame two');
        });

        self::assertPageContains($session, 'Main frame');
        self::assertPageNotContains($session, 'Eye frame');
        self::assertPageNotContains($session, 'Eye frame two');
    }
}
