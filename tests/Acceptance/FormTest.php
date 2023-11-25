<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Elements\Select;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\WebDriverBy;

class FormTest extends BrowserTestCase
{
    protected static ?Session $sessionInstance = null;
    protected ?Session $session = null;

    public function __construct()
    {
        parent::__construct(...func_get_args());
        self::$sessionInstance ??= (new SessionFactory())->create();
        $this->session = self::$sessionInstance;
    }

    public function testInputWithoutNameCanBeFilled(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::xpath('/html/body/form[1]/fieldset[1]/label/input');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithNameCanBeFoundAndSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name1');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithIdCanBeFoundAndFilled(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::id('id1');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithClassCanBeFoundAndFilled(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::className('class1');

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithDefaultValueCanBeFoundAndFilled(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::cssSelector('[value=value1]');

        self::assertFieldContains($this->session, 'value1', $selector);

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testInputWithDefaultValueAndNameCanBeSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name2');

        self::assertFieldContains($this->session, 'value2', $selector);

        $this->session->fillField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testHiddenInputCanNotBeFilledInteractively(): void
    {
        $this->expectException(HiddenInputCanNotBeFilledException::class);

        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name3');

        $this->session->fillField($selector, 'testString1');
    }

    public function testHiddenInputCanBeSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name3');

        $this->session->fillHiddenField($selector, 'testString1');

        self::assertFieldContains($this->session, 'testString1', $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($this->session, 'testString1', $selector);
    }

    public function testCheckboxesCanBeSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('check1[]');

        self::assertCheckboxesAreChecked($this->session, ['2', '3'], $selector);

        $checkboxes = $this->session->getCheckboxes($selector);
        $checkboxes->deselectAll();

        self::assertCheckboxesAreChecked($this->session, [], $selector);

        $checkboxes->selectByValue('3');

        self::assertCheckboxesAreChecked($this->session, ['3'], $selector);

        $checkboxes->selectByIndex(3);

        self::assertCheckboxesAreChecked($this->session, ['3', '4'], $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

        self::assertCheckboxesAreChecked($this->session, ['3', '4'], $selector);
    }

    public function testRadiosCanBeSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('radio1');

        self::assertRadioIsSelected($this->session, '2', $selector);

        $radios = $this->session->getRadios($selector);

        $radios->selectByValue('4');

        self::assertRadioIsSelected($this->session, '4', $selector);

        $this->session->submitForm(WebDriverBy::name('form1'));

        self::assertRadioIsSelected($this->session, '4', $selector);
    }

    public function testSelectSingleCanBeSubmitted(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('select1');

        $select = $this->session->getSelect($selector);

        self::assertInstanceOf(Select::class, $select);

        $selectedValues = $select->getSelectedValues();

        self::assertSame(['chrome' => '2', 'firefox' => '2'], $selectedValues);
        self::assertOptionIsSelectedByText($this->session, 'Value2', $selector);
        self::assertOptionIsSelectedByValue($this->session, '2', $selector);

        $select->selectByVisibleText('Value3');

        $selectedValues = $select->getSelectedValues();

        self::assertEquals(['chrome' => '3', 'firefox' => '3'], $selectedValues);
    }
}
