<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Elements\Parallel\Select;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\WebDriverBy;

class FormTest extends BrowserTestCase
{

    public function testInputWithoutNameCanBeFilled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::xpath('/html/body/form[1]/fieldset[1]/label/input');

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testInputWithNameCanBeFoundAndSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name1');

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);

        $session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testInputWithIdCanBeFoundAndFilled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::id('id1');

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testInputWithClassCanBeFoundAndFilled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::className('class1');

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testInputWithDefaultValueCanBeFoundAndFilled(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::cssSelector('[value=value1]');

        self::assertFieldContains($session, 'value1', $selector);

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testInputWithDefaultValueAndNameCanBeSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name2');

        self::assertFieldContains($session, 'value2', $selector);

        $session->fillField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);

        $session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testHiddenInputCanNotBeFilledInteractively(): void
    {
        $this->expectException(HiddenInputCanNotBeFilledException::class);

        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name3');

        $session->fillField($selector, 'testString1');
    }

    public function testHiddenInputCanBeSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name3');

        $session->fillHiddenField($selector, 'testString1');

        self::assertFieldContains($session, 'testString1', $selector);

        $session->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($session, 'testString1', $selector);
    }

    public function testCheckboxesCanBeSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('check1[]');

        self::assertCheckboxesAreChecked($session, ['2', '3'], $selector);

        $checkboxes = $session->getCheckboxes($selector);
        $checkboxes->deselectAll();

        self::assertCheckboxesAreChecked($session, [], $selector);

        $checkboxes->selectByValue('3');

        self::assertCheckboxesAreChecked($session, ['3'], $selector);

        $checkboxes->selectByIndex(3);

        self::assertCheckboxesAreChecked($session, ['3', '4'], $selector);

        $session->submitForm(WebDriverBy::name('form1'));

        self::assertCheckboxesAreChecked($session, ['3', '4'], $selector);
    }

    public function testRadiosCanBeSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('radio1');

        self::assertRadioIsSelected($session, '2', $selector);

        $radios = $session->getRadios($selector);

        $radios->selectByValue('4');

        self::assertRadioIsSelected($session, '4', $selector);

        $session->submitForm(WebDriverBy::name('form1'));

        self::assertRadioIsSelected($session, '4', $selector);
    }

    public function testSelectSingleCanBeSubmitted(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('select1');

        $select = $session->getSelect($selector);

        self::assertInstanceOf(Select::class, $select);

        $selectedValues = $select->getSelectedValues();

        self::assertSame(['chrome' => '2', 'firefox' => '2'], $selectedValues);
        self::assertOptionIsSelectedByText($session, 'Value2', $selector);
        self::assertOptionIsSelectedByValue($session, '2', $selector);

        $select->selectByVisibleText('Value3');

        $selectedValues = $select->getSelectedValues();

        self::assertEquals(['chrome' => '3', 'firefox' => '3'], $selectedValues);
    }

    public function testFormCanBeSubmittedWithValueData(): void
    {
        $session = (new SessionFactory())->create('session1');
        $session->get('https://web.local.co-stack-test.com/form.php');

        $formSelector = WebDriverBy::name('form1');

        $formData = [
            'name1' => 'testString1',
            'name2' => 'testString2',
            'check1[]' => ['2', '4'],
            'radio1' => '3',
            'select1' => '3',
            'select2[]' => ['1', '3']
        ];
        $session->submitForm($formSelector, $formData);

        $formDataWithDefaults = array_merge($formData, [
            'button1input' => '',
        ]);


        self::assertFormDataEquals($session, $formDataWithDefaults, $formSelector);
    }
}
