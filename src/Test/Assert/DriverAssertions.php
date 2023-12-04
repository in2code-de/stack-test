<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Assert;

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
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\WebDriverBy;

trait DriverAssertions
{
    public static function assertElementContains(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new ElementContains($driver, $selector));
    }

    public static function assertElementEquals(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new ElementEquals($driver, $selector));
    }

    public static function assertElementNotContains(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new ElementNotContains($driver, $selector));
    }

    public static function assertElementNotEquals(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new ElementNotEquals($driver, $selector));
    }

    public static function assertPageContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageContains($driver));
    }

    public static function assertPageNotContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageNotContains($driver));
    }

    public static function assertCookieIsEqual(WebDriver $driver, Cookie $cookie): void
    {
        self::assertThat($cookie, new CookieIsEqual($driver));
    }

    public static function assertCookieIsSet(WebDriver $driver, Cookie|string $cookie): void
    {
        self::assertThat($cookie, new CookieIsSet($driver));
    }

    public static function assertCookieIsSame(WebDriver $driver, Cookie $cookie): void
    {
        self::assertThat($cookie, new CookieIsSame($driver));
    }

    public static function assertCookieIsNotSet(WebDriver $driver, Cookie|string $cookie): void
    {
        self::assertThat($cookie, new CookieIsNotSet($driver));
    }

    public static function assertCurrentUrlContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new CurrentUrlContains($driver));
    }

    public static function assertCurrentUrlEquals(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new CurrentUrlEquals($driver));
    }

    public static function assertCurrentUrlNotContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotContains($driver));
    }

    public static function assertCurrentUrlNotEquals(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotEquals($driver));
    }

    public static function assertElementExists(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementExists($driver));
    }

    public static function assertElementNotExists(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementNotExists($driver));
    }

    public static function assertLinkExists(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new LinkExists($driver));
    }

    public static function assertLinkNotExists(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new LinkNotExists($driver));
    }

    public static function assertCheckboxesAreChecked(
        WebDriver $driver,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreChecked($driver, $checkboxSelector));
    }

    public static function assertCheckboxesAreNotChecked(
        WebDriver $driver,
        WebDriverBy|string|array $valuesOrSelector,
        WebDriverBy $checkboxSelector,
    ): void {
        self::assertThat($valuesOrSelector, new CheckboxesAreNotChecked($driver, $checkboxSelector));
    }

    public static function assertCheckboxIsChecked(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsChecked($driver));
    }

    public static function assertCheckboxIsNotChecked(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsNotChecked($driver));
    }

    public static function assertRadioIsNotSelected(
        WebDriver $driver,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsNotSelected($driver, $radioSelector));
    }

    public static function assertRadioIsSelected(
        WebDriver $driver,
        WebDriverBy|string $valueTextOrSelector,
        WebDriverBy $radioSelector,
    ): void {
        self::assertThat($valueTextOrSelector, new RadioIsSelected($driver, $radioSelector));
    }

    public static function assertFieldContains(WebDriver $driver, string $string, WebDriverBy $fieldSelector): void
    {
        self::assertThat($string, new FieldContains($driver, $fieldSelector));
    }

    public static function assertFieldEquals(WebDriver $driver, string $string, WebDriverBy $fieldSelector): void
    {
        self::assertThat($string, new FieldEquals($driver, $fieldSelector));
    }

    public static function assertFieldNotContains(WebDriver $driver, string $string, WebDriverBy $fieldSelector): void
    {
        self::assertThat($string, new FieldNotContains($driver, $fieldSelector));
    }

    public static function assertFieldNotEquals(WebDriver $driver, string $string, WebDriverBy $fieldSelector): void
    {
        self::assertThat($string, new FieldNotEquals($driver, $fieldSelector));
    }

    public static function assertOptionIsSelectedByText(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new OptionIsSelectedByText($driver, $selector));
    }

    public static function assertOptionIsSelectedByValue(WebDriver $driver, string $string, WebDriverBy $selector): void
    {
        self::assertThat($string, new OptionIsSelectedByValue($driver, $selector));
    }

    public static function OptionsAreCheckedByText(WebDriver $driver, string $string, WebDriverBy $selectSelector): void
    {
        self::assertThat($string, new OptionsAreCheckedByText($driver, $selectSelector));
    }

    public static function assertOptionsAreCheckedByValue(
        WebDriver $driver,
        string $value,
        WebDriverBy $selectSelector,
    ): void {
        self::assertThat($value, new OptionsAreCheckedByValue($driver, $selectSelector));
    }

    public static function assertFormDataEquals(WebDriver $driver, array $value, WebDriverBy $selectSelector): void
    {
        self::assertThat($value, new FormDataEquals($driver, $selectSelector));
    }

    public static function assertPageTitleContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageTitleContains($driver));
    }

    public static function assertPageTitleEquals(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageTitleEquals($driver));
    }

    public static function assertPageTitleNotContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageTitleNotContains($driver));
    }

    public static function assertPageTitleNotEquals(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new PageTitleNotEquals($driver));
    }

    public static function assertSourceContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new SourceContains($driver));
    }

    public static function assertSourceNotContains(WebDriver $driver, string $string): void
    {
        self::assertThat($string, new SourceNotContains($driver));
    }

    public static function assertElementIsNotVisible(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementIsNotVisible($driver));
    }

    public static function assertElementIsVisible(WebDriver $driver, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementIsVisible($driver));
    }

    public static function assertElementIsVisibleInElement(
        WebDriver $driver,
        WebDriverBy $childSelector,
        WebDriverBy $parentSelector,
    ): void {
        self::assertThat($childSelector, new ElementIsVisibleInElement($driver, $parentSelector));
    }
}
