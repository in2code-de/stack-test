<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\WebDriverBy;

class FormTest extends BrowserTestCase
{
    protected Session $session;
    protected function setUp(): void
    {
        parent::setUp();
        $sessionFactory = new SessionFactory();
        $this->session = $sessionFactory->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->session);
    }

    public function testInputWithoutNameCanBeFilled(): void
    {
        $selector = WebDriverBy::xpath('/html/body/form[1]/fieldset[1]/label/input');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithNameCanBeFoundAndSubmitted(): void
    {
        $selector = WebDriverBy::name('name1');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

    }
}
