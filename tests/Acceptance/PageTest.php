<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\Factory;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    use DriverAssertions;

    public function testPageTitle(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        self::assertPageTitleEquals($driver, 'Testing Form for test-stack');
        self::assertPageTitleContains($driver, 'Form for test-stack');
        self::assertPageTitleNotEquals($driver, 'Testing Form for phpunit');
        self::assertPageTitleNotContains($driver, 'fooo');
    }

    public function testPageHistoryNavigation(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/test.php');

        self::assertCurrentUrlEquals($driver, 'https://web.local.co-stack-test.com/test.php');

        $driver->findElement(WebDriverBy::linkText('test2'))->click();

        self::assertCurrentUrlEquals($driver, 'https://web.local.co-stack-test.com/test2.php');

        $driver->navigate()->refresh();

        self::assertCurrentUrlEquals($driver, 'https://web.local.co-stack-test.com/test2.php');

        $driver->navigate()->back();

        self::assertCurrentUrlEquals($driver, 'https://web.local.co-stack-test.com/test.php');

        $driver->navigate()->forward();

        self::assertCurrentUrlEquals($driver, 'https://web.local.co-stack-test.com/test2.php');
    }
}
