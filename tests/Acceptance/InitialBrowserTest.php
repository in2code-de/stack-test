<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;

class InitialBrowserTest extends BrowserTestCase
{
    public function testOpeningUrl(): void
    {
        $session = $this->sessionFactory->create();
        $session->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($session, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $session->setCookie($cookie);

        self::assertSetCookieIsEqual($session, $cookie);

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

        // Show element IDs for each browser
        $elements = $session->findElements(WebDriverBy::name('fuuu'));
        foreach ($elements->getElementsPerDriver() as $browserName => $elements) {
            foreach ($elements as $element) {
                echo $element->getID() . ' in ' . $browserName . PHP_EOL;
            }
        }
        // Prevent phpunit error "output while test"
        ob_flush();

        self::assertFieldContains($session, 'blabla', WebDriverBy::name('huahsd'));
        self::assertFieldNotContains($session, 'igigigi', WebDriverBy::name('huahsd'));

        $session->fillField(WebDriverBy::name('huahsd'), 'igigigi');
        self::assertFieldContains($session, 'igigigi', WebDriverBy::name('huahsd'));

        $session->clearField(WebDriverBy::name('huahsd'));
        self::assertFieldNotContains($session, 'igigigi', WebDriverBy::name('huahsd'));

        self::assertOptionIsSelected($session, WebDriverBy::name('kfsljd'), 'Bar');
        $session->selectOption(WebDriverBy::name('kfsljd'), 'Foo');
        self::assertOptionIsSelected($session, WebDriverBy::name('kfsljd'), 'Foo');

        //$session->checkOption()
        //$session->type()
        //$session->attachFile()
        //$session->getAttribute()
        //$session->filterByAttributes()
        //$session->optionIsSelected()
        //$session->seeInTitle()
        //$session->seeNotInTitle()
        //$session->acceptPopup()
        //$session->cancelPopup()
        //$session->seeInPopup()
        //$session->dontSeeInPopup()
        //$session->typeInPopup()
        //$session->reloadPage()
        //$session->moveBack()
        //$session->moveForward()
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
        //executeJS
        //executeAsyncJS
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
