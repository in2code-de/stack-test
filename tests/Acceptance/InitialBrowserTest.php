<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests\Acceptance;

use CoStack\StackTest\BrowserTestCase;
use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Session;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;

class InitialBrowserTest extends BrowserTestCase
{
    protected static ?Session $sessionInstance = null;
    protected ?Session $session = null;

    public function __construct(string $name)
    {
        parent::__construct($name);
        self::$sessionInstance ??= (new SessionFactory())->create();
        $this->session = self::$sessionInstance;
    }

    public function testOpeningUrl(): void
    {
        $this->session->get('https://web.local.co-stack-test.com/test.php');

        self::assertPageContains($this->session, 'hi there');

        $cookie = new Cookie('coke', 'matsch');
        $cookie->setSecure(true);
        $this->session->setCookie($cookie);

        self::assertSetCookieIsEqual($this->session, $cookie);

        self::assertPageNotContains($this->session, 'hella');

        self::assertElementContains($this->session, 'hi there', WebDriverBy::cssSelector('body'));
        self::assertElementNotContains($this->session, 'hella', WebDriverBy::cssSelector('body'));
        self::assertElementExists($this->session, WebDriverBy::cssSelector('body'));
        self::assertElementNotExists($this->session, WebDriverBy::cssSelector('custom-element'));

        $this->session->click(WebDriverBy::linkText('test2'));

        self::assertPageContains($this->session, 'PHP Version 8.1');
        self::assertCurrentUrlContains($this->session, 'est2');
        self::assertCurrentUrlNotContains($this->session, 'test.php');

        self::assertCheckboxIsChecked($this->session, WebDriverBy::name('fuuu'));
        self::assertCheckboxIsNotChecked($this->session, WebDriverBy::name('faaa'));

        //// Show element IDs for each browser
        //$elements = $this->session->findElements(WebDriverBy::name('fuuu'));
        //foreach ($elements->getElementsPerDriver() as $browserName => $elements) {
        //    foreach ($elements as $element) {
        //        echo $element->getID() . ' in ' . $browserName . PHP_EOL;
        //    }
        //}
        //// Prevent phpunit error "output while test"
        //ob_flush();

        $fieldSelector = WebDriverBy::name('huahsd');

        self::assertFieldContains($this->session, 'blabla', $fieldSelector);
        self::assertFieldNotContains($this->session, 'igigigi', $fieldSelector);

        $this->session->fillField($fieldSelector, 'igigigi');
        self::assertFieldContains($this->session, 'igigigi', $fieldSelector);

        $this->session->clearField($fieldSelector);
        self::assertFieldNotContains($this->session, 'igigigi', $fieldSelector);

        $selectSelector = WebDriverBy::name('kfsljd');

        self::assertOptionIsSelectedByText($this->session, 'Bar', $selectSelector);
        self::assertOptionIsSelectedByValue($this->session, '1', $selectSelector);

        $this->session->selectOption($selectSelector, 'Foo');

        self::assertOptionIsSelectedByText($this->session, 'Foo', $selectSelector);
        self::assertOptionIsSelectedByValue($this->session, '', $selectSelector);

        //$this->session->checkOption()
        //$this->session->type()
        //$this->session->attachFile()
        //$this->session->getAttribute()
        //$this->session->filterByAttributes()
        //$this->session->optionIsSelected()
        //$this->session->seeInTitle()
        //$this->session->seeNotInTitle()
        //$this->session->acceptPopup()
        //$this->session->cancelPopup()
        //$this->session->seeInPopup()
        //$this->session->dontSeeInPopup()
        //$this->session->typeInPopup()
        //$this->session->reloadPage()
        //$this->session->moveBack()
        //$this->session->moveForward()
        //$this->session->submitForm()
        //$this->session->waitForElementChange()
        //$this->session->waitForElement()
        //$this->session->waitForElementVisible()
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
