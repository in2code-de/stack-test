<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Factory\SessionFactory;
use CoStack\StackTest\Test\Constraint\Content\ElementContains;
use CoStack\StackTest\Test\Constraint\Content\ElementNotContains;
use CoStack\StackTest\Test\Constraint\Content\PageContains;
use CoStack\StackTest\Test\Constraint\Content\PageNotContains;
use CoStack\StackTest\Test\Constraint\Cookie\SetCookieIsEqual;
use CoStack\StackTest\Test\Constraint\Cookie\SetCookieIsSame;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlContains;
use CoStack\StackTest\Test\Constraint\CurrentUrl\CurrentUrlNotContains;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use CoStack\StackTest\Test\Constraint\Existence\ElementNotExists;
use CoStack\StackTest\Test\Constraint\Existence\LinkExists;
use CoStack\StackTest\Test\Constraint\Existence\LinkNotExists;
use CoStack\StackTest\Test\Constraint\Form\CheckboxIsChecked;
use CoStack\StackTest\Test\Constraint\Form\CheckboxIsNotChecked;
use CoStack\StackTest\Test\Constraint\Form\FieldContains;
use CoStack\StackTest\Test\Constraint\Form\FieldNotContains;
use CoStack\StackTest\Test\Constraint\Form\OptionIsSelected;
use CoStack\StackTest\Test\Constraint\Source\SourceContains;
use CoStack\StackTest\Test\Constraint\Source\SourceNotContains;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;

abstract class BrowserTestCase extends TestCase
{
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

    protected static function assertElementContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementContains($session, $selector));
    }

    protected static function assertElementNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new ElementNotContains($session, $selector));
    }

    protected static function assertElementExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementExists($session));
    }

    protected static function assertElementNotExists(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new ElementNotExists($session));
    }

    protected static function assertSourceContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceContains($session));
    }

    protected static function assertSourceNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new SourceNotContains($session));
    }

    protected static function assertLinkExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkExists($session));
    }

    protected static function assertLinkNotExists(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new LinkNotExists($session));
    }

    protected static function assertCurrentUrlContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlContains($session));
    }

    protected static function assertCurrentUrlNotContains(Session|RemoteWebDriver $session, string $string): void
    {
        self::assertThat($string, new CurrentUrlNotContains($session));
    }

    protected static function assertCheckboxIsChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsChecked($session));
    }

    protected static function assertCheckboxIsNotChecked(Session|RemoteWebDriver $session, WebDriverBy $selector): void
    {
        self::assertThat($selector, new CheckboxIsNotChecked($session));
    }

    protected static function assertFieldContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new FieldContains($session, $selector));
    }

    protected static function assertFieldNotContains(
        Session|RemoteWebDriver $session,
        string $string,
        WebDriverBy $selector,
    ): void {
        self::assertThat($string, new FieldNotContains($session, $selector));
    }

    protected static function assertOptionIsSelected(
        Session|RemoteWebDriver $session,
        WebDriverBy $selector,
        WebDriverBy|string $option,
    ): void {
        self::assertThat($option, new OptionIsSelected($session, $selector));
    }
}
