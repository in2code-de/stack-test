<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverAlert;
use Facebook\WebDriver\WebDriverBy;

class AlertTest extends BrowserTestCase
{
    public function testAlertPopupTextCanBeTested(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/alert.php');

        $session->inPopupContext(function (RemoteWebDriver $driver, WebDriverAlert $alert): void {
            $text = $alert->getText();
            self::assertSame('Test alert Message', $text);
            $alert->accept();
        });
    }

    public function testConfirmPopupCanBeAccepted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/confirm.php');

        $session->inPopupContext(function (RemoteWebDriver $driver, WebDriverAlert $alert): void {
            $text = $alert->getText();
            self::assertSame('Test confirm Message', $text);
            $alert->accept();
        });

        self::assertElementEquals($session, 'Confirmed', WebDriverBy::id('confirmation'));
    }

    public function testConfirmPopupCanBeDismissed(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/confirm.php');

        $session->inPopupContext(function (RemoteWebDriver $driver, WebDriverAlert $alert): void {
            $text = $alert->getText();
            self::assertSame('Test confirm Message', $text);
            $alert->dismiss();
        });

        self::assertElementEquals($session, 'Declined', WebDriverBy::id('confirmation'));
    }

    public function testPromptPopupCanBeFilled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/prompt.php');

        $session->inPopupContext(function (RemoteWebDriver $driver, WebDriverAlert $alert): void {
            $text = $alert->getText();
            self::assertSame('Enter your test message', $text);
            $alert->sendKeys('My test string 1');
            $alert->accept();
        });

        self::assertElementEquals($session, 'My test string 1', WebDriverBy::id('prompt-result'));
    }

    public function testPromptPopupCanBeCancelled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/prompt.php');

        $session->inPopupContext(function (RemoteWebDriver $driver, WebDriverAlert $alert): void {
            $text = $alert->getText();
            self::assertSame('Enter your test message', $text);
            $alert->sendKeys('My test string 1');
            $alert->dismiss();
        });

        self::assertElementEquals($session, '', WebDriverBy::id('prompt-result'));
    }
}
