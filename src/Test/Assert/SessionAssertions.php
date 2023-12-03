<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Assert;

use CoStack\StackTest\Session\Session;
use CoStack\StackTest\Test\Constraint\Content\ElementContains;
use CoStack\StackTest\Test\Constraint\Content\ElementEquals;
use CoStack\StackTest\Test\Constraint\Content\ElementNotContains;
use CoStack\StackTest\Test\Constraint\Content\ElementNotEquals;
use CoStack\StackTest\Test\Constraint\Content\PageContains;
use CoStack\StackTest\Test\Constraint\Content\PageNotContains;
use CoStack\StackTest\Test\Constraint\Cookie\CookieIsEqual;
use CoStack\StackTest\Test\Constraint\Cookie\CookieIsNotSet;
use CoStack\StackTest\Test\Constraint\Cookie\CookieIsSame;
use CoStack\StackTest\Test\Constraint\Cookie\CookieIsSet;
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
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleContains;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleEquals;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleNotContains;
use CoStack\StackTest\Test\Constraint\Page\Title\PageTitleNotEquals;
use CoStack\StackTest\Test\Constraint\Source\SourceContains;
use CoStack\StackTest\Test\Constraint\Source\SourceNotContains;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisible;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsVisibleInElement;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

trait SessionAssertions
{
    public static function assertElementContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementContains($session, $selector));
    }

    public static function assertElementEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementEquals($session, $selector));
    }

    public static function assertElementNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementNotContains($session, $selector));
    }

    public static function assertElementNotEquals(
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

    public static function assertCookieIsEqual(Session|RemoteWebDriver $session, Cookie $cookie): void
    {
        self::assertThat($cookie, new CookieIsEqual($session));
    }

    public static function assertCookieIsSet(Session|RemoteWebDriver $session, Cookie|string $cookie): void
    {
        self::assertThat($cookie, new CookieIsSet($session));
    }

    public static function assertCookieIsSame(Session|RemoteWebDriver $session, Cookie $cookie): void
    {
        self::assertThat($cookie, new CookieIsSame($session));
    }

    public static function assertCookieIsNotSet(Session|RemoteWebDriver $session, Cookie|string $cookie): void
    {
        self::assertThat($cookie, new CookieIsNotSet($session));
    }

    public static function assertCurrentUrlContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlContains($session));
    }

    public static function assertCurrentUrlEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlEquals($session));
    }

    public static function assertCurrentUrlNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotContains($session));
    }

    public static function assertCurrentUrlNotEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotEquals($session));
    }

    public static function assertElementExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementExists($session));
    }

    public static function assertElementNotExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementNotExists($session));
    }

    public static function assertLinkExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkExists($session));
    }

    public static function assertLinkNotExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkNotExists($session));
    }

    public static function assertCheckboxesAreChecked(
        Session|RemoteWebDriver $session,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreChecked($session, $checkboxSelector));
    }

    public static function assertCheckboxesAreNotChecked(
        Session|RemoteWebDriver $session,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreNotChecked($session, $checkboxSelector));
    }

    public static function assertCheckboxIsChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsChecked($session));
    }

    public static function assertCheckboxIsNotChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsNotChecked($session));
    }

    public static function assertRadioIsNotSelected(
        Session|RemoteWebDriver $session,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsNotSelected($session, $radioSelector));
    }

    public static function assertRadioIsSelected(
        Session|RemoteWebDriver $session,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsSelected($session, $radioSelector));
    }

    public static function assertFieldContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldContains($session, $fieldSelector));
    }

    public static function assertFieldEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldEquals($session, $fieldSelector));
    }

    public static function assertFieldNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldNotContains($session, $fieldSelector));
    }

    public static function assertFieldNotEquals(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $fieldSelector,
    ): void {
        self::assertThat($string, new FieldNotEquals($session, $fieldSelector));
    }

    public static function assertOptionIsSelectedByText(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new OptionIsSelectedByText($session, $selector));
    }

    public static function assertOptionIsSelectedByValue(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new OptionIsSelectedByValue($session, $selector));
    }

    public static function OptionsAreCheckedByText(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($string, new OptionsAreCheckedByText($session, $selectSelector));
    }

    public static function assertOptionsAreCheckedByValue(
        Session|RemoteWebDriver $session,
        string $value,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($value, new OptionsAreCheckedByValue($session, $selectSelector));
    }

    public static function assertFormDataEquals(
        Session|RemoteWebDriver $session,
        array $value,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($value, new FormDataEquals($session, $selectSelector));
    }

    public static function assertPageTitleContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleContains($session));
    }

    public static function assertPageTitleEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleEquals($session));
    }

    public static function assertPageTitleNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleNotContains($session));
    }

    public static function assertPageTitleNotEquals(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new PageTitleNotEquals($session));
    }

    public static function assertSourceContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceContains($session));
    }

    public static function assertSourceNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceNotContains($session));
    }

    public static function assertElementIsNotVisible(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementIsNotVisible($session));
    }

    public static function assertElementIsVisible(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementIsVisible($session));
    }

    public static function assertElementIsVisibleInElement(
        Session|RemoteWebDriver $session,
        WebDriverBy $childSelector,
        WebDriverBy $parentSelector,
    ): void {
        self::assertThat($childSelector, new ElementIsVisibleInElement($session, $parentSelector));
    }
}
