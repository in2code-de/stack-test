<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;

class InitialBrowserTest extends BrowserTestCase
{
    public function testOpeningUrl(): void
    {
        $session = SessionFactory::getInstance()->create('session1');
        $session->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($session, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $session->setCookie($cookie);

        self::assertCookieIsEqual($session, $cookie);

        self::assertPageNotContains($session, 'hella');

        self::assertElementContains($session, 'hi there', WebDriverBy::cssSelector('body'));
        self::assertElementNotContains($session, 'hella', WebDriverBy::cssSelector('body'));
        self::assertElementExists($session, WebDriverBy::cssSelector('body'));
        self::assertElementNotExists($session, WebDriverBy::cssSelector('custom-element'));

        $session->click(WebDriverBy::linkText('test2'));

        self::assertPageContains($session, 'PHP Version 8.1');
        self::assertCurrentUrlContains($session, 'est2');
        self::assertCurrentUrlNotContains($session, 'test.php');

        self::assertCheckboxIsChecked($session, WebDriverBy::name('fuuu'));
        self::assertCheckboxIsNotChecked($session, WebDriverBy::name('faaa'));

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

        self::assertFieldContains($session, 'blabla', $fieldSelector);
        self::assertFieldNotContains($session, 'igigigi', $fieldSelector);

        $session->fillField($fieldSelector, 'igigigi');
        self::assertFieldContains($session, 'igigigi', $fieldSelector);

        $session->clearField($fieldSelector);
        self::assertFieldNotContains($session, 'igigigi', $fieldSelector);

        $selectSelector = WebDriverBy::name('kfsljd');

        self::assertOptionIsSelectedByText($session, 'Bar', $selectSelector);
        self::assertOptionIsSelectedByValue($session, '1', $selectSelector);

        $session->selectOption($selectSelector, 'Foo');

        self::assertOptionIsSelectedByText($session, 'Foo', $selectSelector);
        self::assertOptionIsSelectedByValue($session, '', $selectSelector);

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
