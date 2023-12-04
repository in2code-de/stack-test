<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\Test\Assert\DriverAssertions;
use CoStack\StackTest\WebDriver\Factory;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use PHPUnit\Framework\TestCase;

class InitialBrowserTest extends TestCase
{
    use DriverAssertions;

    public function testOpeningUrl(): void
    {
        $driver = Factory::getInstance()->createMultiDriver('session1');
        $driver->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($driver, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $driver->manage()->addCookie($cookie);

        self::assertCookieIsEqual($driver, $cookie);

        self::assertPageNotContains($driver, 'hella');

        self::assertElementContains($driver, 'hi there', WebDriverBy::cssSelector('body'));
        self::assertElementNotContains($driver, 'hella', WebDriverBy::cssSelector('body'));
        self::assertElementExists($driver, WebDriverBy::cssSelector('body'));
        self::assertElementNotExists($driver, WebDriverBy::cssSelector('custom-element'));

        $driver->findElement(WebDriverBy::linkText('test2'))->click();

        self::assertPageContains($driver, 'PHP Version 8.1');
        self::assertCurrentUrlContains($driver, 'est2');
        self::assertCurrentUrlNotContains($driver, 'test.php');

        self::assertCheckboxIsChecked($driver, WebDriverBy::name('fuuu'));
        self::assertCheckboxIsNotChecked($driver, WebDriverBy::name('faaa'));

        //// Show element IDs for each browser
        //$elements = $session->findElements(WebDriverBy::name('fuuu'));
        //foreach ($elements->getElementsPerDriver() as $browserName => $elements) {
        //    foreach ($elements as $element) {
        //        echo $element->getID() . ' in ' . $browserName . PHP_EOL;
        //    }
        //}
        //// Prevent phpunit error "output while test"
        //ob_flush();

        $fieldSelector = WebDriverBy::name('huahsd');

        self::assertFieldContains($driver, 'blabla', $fieldSelector);
        self::assertFieldNotContains($driver, 'igigigi', $fieldSelector);

        $driver->fillField($fieldSelector, 'igigigi');
        self::assertFieldContains($driver, 'igigigi', $fieldSelector);

        $driver->clearField($fieldSelector);
        self::assertFieldNotContains($driver, 'igigigi', $fieldSelector);

        $selectSelector = WebDriverBy::name('kfsljd');

        self::assertOptionIsSelectedByText($driver, 'Bar', $selectSelector);
        self::assertOptionIsSelectedByValue($driver, '1', $selectSelector);

        foreach ($driver->drivers as $remoteWebDriver) {
            $select = new WebDriverSelect($remoteWebDriver->findElement($selectSelector));
            $select->selectByVisibleText('Foo');
        }

        self::assertOptionIsSelectedByText($driver, 'Foo', $selectSelector);
        self::assertOptionIsSelectedByValue($driver, '', $selectSelector);

        //$session->checkOption()
        //$session->type()
        //$session->attachFile()
        //$session->getAttribute()
        //$session->filterByAttributes()
        //$session->submitForm()
        //$session->waitForElementChange()
        //$session->waitForElement()
        //$session->waitForElementVisible()
        //waitForElementNotVisible
        //waitForElementClickable
        //waitForText
        //wait
        //executeInSelenium
        //switchToWindow
        //switchToIFrame
        //switchToFrame
        //findAndSwitchToFrame
        //waitForJS
        //maximizeWindow
        //dragAndDrop
        //moveMouseOver
        //clickWithLeftButton
        //clickWithRightButton
        //doubleClick
        //pressKey
        //scrollTo
        //openNewTab
        //seeNumberOfTabs
        //closeTab
        //switchToNextTab
        //switchToPreviousTab
        //getRelativeTabHandle
    }
}
