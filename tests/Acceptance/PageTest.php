<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use Facebook\WebDriver\WebDriverBy;

class PageTest extends BrowserTestCase
{
    public function testPageTitle(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        self::assertPageTitleEquals($session, 'Testing Form for test-stack');
        self::assertPageTitleContains($session, 'Form for test-stack');
        self::assertPageTitleNotEquals($session, 'Testing Form for phpunit');
        self::assertPageTitleNotContains($session, 'fooo');
    }

    public function testPageHistoryNavigation(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/test.php');

        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/test.php');

        $session->click(WebDriverBy::linkText('test2'));

        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/test2.php');

        $session->refresh();

        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/test2.php');

        $session->back();

        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/test.php');

        $session->forward();

        self::assertCurrentUrlEquals($session, 'https://web.local.co-stack-test.com/test2.php');
    }
}
