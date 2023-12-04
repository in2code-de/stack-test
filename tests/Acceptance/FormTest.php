<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Elements\Single\Checkboxes;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\Factory;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    use DriverAssertions;

    public function testInputWithoutNameCanBeFilled(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::xpath('/html/body/form[1]/fieldset[1]/label/input');

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testInputWithNameCanBeFoundAndSubmitted(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name1');

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);

        $driver->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testInputWithIdCanBeFoundAndFilled(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::id('id1');

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testInputWithClassCanBeFoundAndFilled(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::className('class1');

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testInputWithDefaultValueCanBeFoundAndFilled(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::cssSelector('[value=value1]');

        self::assertFieldContains($driver, 'value1', $selector);

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testInputWithDefaultValueAndNameCanBeSubmitted(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name2');

        self::assertFieldContains($driver, 'value2', $selector);

        $driver->fillField($selector, 'testString1');

        self::assertFieldContains($driver, 'testString1', $selector);

        $driver->submitForm(WebDriverBy::name('form1'));

        self::assertFieldContains($driver, 'testString1', $selector);
    }

    public function testHiddenInputCanNotBeFilledInteractively(): void
    {
        $this->expectException(HiddenInputCanNotBeFilledException::class);

        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('name3');

        $driver->fillField($selector, 'testString1');
    }

    public function testCheckboxesCanBeSubmitted(): void
    {
        $this->markTestSkipped('Not migrated yet');

        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('check1[]');

        self::assertCheckboxesAreChecked($driver, ['2', '3'], $selector);

        $checkboxes = $driver->findElement($selector);
        $checkboxes->deselectAll();

        self::assertCheckboxesAreChecked($driver, [], $selector);

        $checkboxes->selectByValue('3');

        self::assertCheckboxesAreChecked($driver, ['3'], $selector);

        $checkboxes->selectByIndex(3);

        self::assertCheckboxesAreChecked($driver, ['3', '4'], $selector);

        $driver->submitForm(WebDriverBy::name('form1'));

        self::assertCheckboxesAreChecked($driver, ['3', '4'], $selector);
    }

    public function testRadiosCanBeSubmitted(): void
    {
        $this->markTestSkipped('Not migrated yet');
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('radio1');

        self::assertRadioIsSelected($driver, '2', $selector);

        $radios = $driver->getRadios($selector);

        $radios->selectByValue('4');

        self::assertRadioIsSelected($driver, '4', $selector);

        $driver->submitForm(WebDriverBy::name('form1'));

        self::assertRadioIsSelected($driver, '4', $selector);
    }

    public function testSelectSingleCanBeSubmitted(): void
    {
        $this->markTestSkipped('Not migrated yet');
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $selector = WebDriverBy::name('select1');

        $select = $driver->getSelect($selector);

        self::assertInstanceOf(Select::class, $select);

        $selectedValues = $select->getSelectedValues();

        self::assertSame(['chrome' => '2', 'firefox' => '2'], $selectedValues);
        self::assertOptionIsSelectedByText($driver, 'Value2', $selector);
        self::assertOptionIsSelectedByValue($driver, '2', $selector);

        $select->selectByVisibleText('Value3');

        $selectedValues = $select->getSelectedValues();

        self::assertEquals(['chrome' => '3', 'firefox' => '3'], $selectedValues);
    }

    public function testFormCanBeSubmittedWithValueData(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/form.php');

        $formSelector = WebDriverBy::name('form1');

        $formData = [
            'name1' => 'testString1',
            'name2' => 'testString2',
            'check1[]' => ['2', '4'],
            'radio1' => '3',
            'select1' => '3',
            'select2[]' => ['1', '3'],
        ];
        $driver->submitForm($formSelector, $formData);

        $formDataWithDefaults = array_merge($formData, [
            'button1input' => '',
        ]);

        self::assertFormDataEquals($driver, $formDataWithDefaults, $formSelector);
    }
}
