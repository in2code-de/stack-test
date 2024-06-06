<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\WebDriverFactory;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class AlertTest extends TestCase
{
    use DriverAssertions;

    public function testAlertPopupTextCanBeTested(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/alert.php');

        $alert = $driver->switchTo()->alert();
        $text = $alert->getText();
        self::assertSame('Test alert Message', $text);
        $alert->accept();
    }

    public function testConfirmPopupCanBeAccepted(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/confirm.php');

        $alert = $driver->switchTo()->alert();
        $text = $alert->getText();
        self::assertSame('Test confirm Message', $text);
        $alert->accept();

        self::assertElementEquals($driver, 'Confirmed', WebDriverBy::id('confirmation'));
    }

    public function testConfirmPopupCanBeDismissed(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/confirm.php');

        $alert = $driver->switchTo()->alert();
        $text = $alert->getText();
        self::assertSame('Test confirm Message', $text);
        $alert->dismiss();

        self::assertElementEquals($driver, 'Declined', WebDriverBy::id('confirmation'));
    }

    public function testPromptPopupCanBeFilled(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/prompt.php');

        $alert = $driver->switchTo()->alert();
        $text = $alert->getText();
        self::assertSame('Enter your test message', $text);
        $alert->sendKeys('My test string 1716902080');
        $alert->accept();

        self::assertElementEquals($driver, 'My test string 1716902080', WebDriverBy::id('prompt-result'));
    }

    public function testPromptPopupCanBeCancelled(): void
    {
        $driver = WebDriverFactory::createChromeDriver();
        $driver->get('https://web.local.co-stack-test.com/prompt.php');

        $alert = $driver->switchTo()->alert();
        $text = $alert->getText();
        self::assertSame('Enter your test message', $text);
        $alert->sendKeys('My test string 1716902091');
        $alert->dismiss();

        self::assertElementEquals($driver, '', WebDriverBy::id('prompt-result'));
    }
}
