<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Elements\Parallel\Alert;
use CoStack\StackTest\Test\Constraint\Content\ElementContains;
use CoStack\StackTest\Test\Constraint\Content\ElementEquals;
use CoStack\StackTest\Test\Constraint\Content\ElementNotContains;
use CoStack\StackTest\Test\Constraint\Content\ElementNotEquals;
use CoStack\StackTest\Test\Constraint\Content\PageContains;
use CoStack\StackTest\Test\Constraint\Content\PageNotContains;
use CoStack\StackTest\Test\Constraint\Cookie\SetCookieIsEqual;
use CoStack\StackTest\Test\Constraint\Cookie\SetCookieIsSame;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlContains;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlEquals;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlNotContains;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlNotEquals;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use CoStack\StackTest\Test\Constraint\Existence\ElementNotExists;
use CoStack\StackTest\Test\Constraint\Existence\LinkExists;
use CoStack\StackTest\Test\Constraint\Existence\LinkNotExists;
use CoStack\StackTest\Test\Constraint\Form\FormDataEquals;
use CoStack\StackTest\Test\Constraint\Form\Input\Check\CheckboxesAreChecked;
use CoStack\StackTest\Test\Constraint\Form\Input\Check\CheckboxesAreNotChecked;
use CoStack\StackTest\Test\Constraint\Form\Input\Check\CheckboxIsChecked;
use CoStack\StackTest\Test\Constraint\Form\Input\Check\CheckboxIsNotChecked;
use CoStack\StackTest\Test\Constraint\Form\Input\Radio\RadioIsNotSelected;
use CoStack\StackTest\Test\Constraint\Form\Input\Radio\RadioIsSelected;
use CoStack\StackTest\Test\Constraint\Form\Input\Text\FieldContains;
use CoStack\StackTest\Test\Constraint\Form\Input\Text\FieldEquals;
use CoStack\StackTest\Test\Constraint\Form\Input\Text\FieldNotContains;
use CoStack\StackTest\Test\Constraint\Form\Input\Text\FieldNotEquals;
use CoStack\StackTest\Test\Constraint\Form\Select\Option\OptionIsSelectedByText;
use CoStack\StackTest\Test\Constraint\Form\Select\Option\OptionIsSelectedByValue;
use CoStack\StackTest\Test\Constraint\Form\Select\Option\OptionsAreCheckedByText;
use CoStack\StackTest\Test\Constraint\Form\Select\Option\OptionsAreCheckedByValue;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertIsNotVisible;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertIsVisible;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertMessageContains;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertMessageEquals;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertMessageNotContains;
use CoStack\StackTest\Test\Constraint\Page\Alert\AlertMessageNotEquals;
use CoStack\StackTest\Test\Constraint\Page\IFrame\CurrentIFrameUrlEquals;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleContains;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleEquals;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleNotContains;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleNotEquals;
use CoStack\StackTest\Test\Constraint\Source\SourceContains;
use CoStack\StackTest\Test\Constraint\Source\SourceNotContains;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

abstract class BrowserTestCase extends TestCase
{
    protected static function assertElementContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementContains($session, $selector));
    }

    protected static function assertElementEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementEquals($session, $selector));
    }

    protected static function assertElementNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementNotContains($session, $selector));
    }

    protected static function assertElementNotEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementNotEquals($session, $selector));
    }

    public static function assertPageContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageContains($session));
    }

    public static function assertPageNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageNotContains($session));
    }

    protected static function assertSetCookieIsEqual(Session|RemoteWebDriver $session, Cookie $cookie): void
    {
        self::assertThat($cookie, new SetCookieIsEqual($session));
    }

    protected static function assertSetCookieIsSame(Session|RemoteWebDriver $session, Cookie $cookie): void
    {
        self::assertThat($cookie, new SetCookieIsSame($session));
    }

    protected static function assertCurrentUrlContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlContains($session));
    }

    protected static function assertCurrentUrlEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlEquals($session));
    }

    protected static function assertCurrentUrlNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotContains($session));
    }

    protected static function assertCurrentUrlNotEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotEquals($session));
    }

    protected static function assertElementExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementExists($session));
    }

    protected static function assertElementNotExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementNotExists($session));
    }

    protected static function assertLinkExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkExists($session));
    }

    protected static function assertLinkNotExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkNotExists($session));
    }

    protected static function assertCheckboxesAreChecked(
        Session|RemoteWebDriver $session,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreChecked($session, $checkboxSelector));
    }

    protected static function assertCheckboxesAreNotChecked(
        Session|RemoteWebDriver $session,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreNotChecked($session, $checkboxSelector));
    }

    protected static function assertCheckboxIsChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsChecked($session));
    }

    protected static function assertCheckboxIsNotChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsNotChecked($session));
    }

    protected static function assertRadioIsNotSelected(
        Session|RemoteWebDriver $session,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsNotSelected($session, $radioSelector));
    }

    protected static function assertRadioIsSelected(
        Session|RemoteWebDriver $session,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsSelected($session, $radioSelector));
    }

    protected static function assertFieldContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldContains($session, $fieldSelector));
    }

    protected static function assertFieldEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldEquals($session, $fieldSelector));
    }

    protected static function assertFieldNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldNotContains($session, $fieldSelector));
    }

    protected static function assertFieldNotEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldNotEquals($session, $fieldSelector));
    }

    protected static function assertOptionIsSelectedByText(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new OptionIsSelectedByText($session, $selector));
    }

    protected static function assertOptionIsSelectedByValue(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new OptionIsSelectedByValue($session, $selector));
    }

    protected static function OptionsAreCheckedByText(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($string, new OptionsAreCheckedByText($session, $selectSelector));
    }

    protected static function assertOptionsAreCheckedByValue(
        Session|RemoteWebDriver $session,
        string $value,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($value, new OptionsAreCheckedByValue($session, $selectSelector));
    }

    protected static function assertFormDataEquals(
        Session|RemoteWebDriver $session,
        array $value,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($value, new FormDataEquals($session, $selectSelector));
    }

    protected static function assertPageTitleContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleContains($session));
    }

    protected static function assertPageTitleEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleEquals($session));
    }

    protected static function assertPageTitleNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleNotContains($session));
    }

    protected static function assertPageTitleNotEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleNotEquals($session));
    }

    protected static function assertSourceContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceContains($session));
    }

    protected static function assertSourceNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceNotContains($session));
    }
}
