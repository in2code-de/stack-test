<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\WebDriverFactory;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class IframeNavigationTest extends TestCase
{
    use DriverAssertions;

    public function testSwitchToIframeAndBack(): void
    {
        $diver = WebDriverFactory::createMultiDriver();
        $diver->get('https://web.local.co-stack-test.com/iframe.php');

        self::assertPageContains($diver, 'Main frame');
        self::assertPageNotContains($diver, 'Eye frame');
        self::assertCurrentUrlEquals($diver, 'https://web.local.co-stack-test.com/iframe.php');

        $diver->inIFrameContext(WebDriverBy::id('content-iframe'), function (WebDriver $driver): void {
            self::assertPageContains($driver, 'Eye frame');
            self::assertPageNotContains($driver, 'Main Frame');
            self::assertPageNotContains($driver, 'Eye frame two');
            $driver->findElement(WebDriverBy::linkText('content 2'))->click();
            self::assertPageContains($driver, 'Eye frame two');
        });

        self::assertPageContains($diver, 'Main frame');
        self::assertPageNotContains($diver, 'Eye frame');
        self::assertPageNotContains($diver, 'Eye frame two');
    }
}
