<?php

declare(strict_types=1);

namespace CoStack\StackTest\Session;

use Closure;
use CoStack\StackTest\Decorator\SessionWait;
use CoStack\StackTest\Elements\Parallel\AbstractSelectable;
use CoStack\StackTest\Elements\Parallel\Checkboxes;
use CoStack\StackTest\Elements\Parallel\Element;
use CoStack\StackTest\Elements\Parallel\Elements;
use CoStack\StackTest\Elements\Parallel\FormElement;
use CoStack\StackTest\Elements\Parallel\Radios;
use CoStack\StackTest\Elements\Parallel\Select;
use CoStack\StackTest\Elements\Single\Form;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use CoStack\StackTest\Routines\Reset\ChromeReset;
use CoStack\StackTest\Routines\Reset\FirefoxReset;
use Exception;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\InvalidSessionIdException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCheckboxes;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverRadios;
use Facebook\WebDriver\WebDriverSelect;

use function array_key_first;
use function implode;
use function in_array;
use function is_string;

class Session
{
    /** @var array<string, RemoteWebDriver> */
    public readonly array $drivers;

    /** @param array<RemoteWebDriver> $drivers */
    public function __construct(
        public readonly string $sessionId,
        array $drivers,
    ) {
        $driversByBrowserName = [];
        foreach ($drivers as $driver) {
            $driversByBrowserName[$driver->getCapabilities()->getBrowserName()] = $driver;
        }
        $this->drivers = $driversByBrowserName;
    }

    /**
     * Elevates a driver to a session
     */
    public static function elevate(Session|RemoteWebDriver $driver): Session
    {
        if ($driver instanceof Session) {
            return $driver;
        }
        return new ElevatedSession($driver);
    }

    /**
     * Deletes all cookies and browser history.
     * This method should be faster than creating a new session for each test.
     */
    public function reset(): void
    {
        foreach ($this->drivers as $browserName => $driver) {
            if (WebDriverBrowserType::CHROME === $browserName) {
                $routine = new ChromeReset();
                $routine->execute($driver);
            }
            if (WebDriverBrowserType::FIREFOX === $browserName) {
                $routine = new FirefoxReset();
                $routine->execute($driver);
            }
        }
    }

    /**
     * Close all browser windows, but not the session
     */
    public function close(): void
    {
        foreach ($this->drivers as $driver) {
            try {
                $driver->close();
                $driver->switchTo()->defaultContent();
            } catch (InvalidSessionIdException) {
                // Ignore errors when calling close multiple times
            }
        }
    }

    public function inEachBrowser(Closure $closure): void
    {
        foreach ($this->drivers as $driver) {
            $subSession = $this->createSubSessionForSingleDriver($driver);
            $closure($subSession);
        }
    }

    public function inOneBrowser(Closure $closure, string $preferredBrowser = null): void
    {
        $selectedDriver = $this->getDriverForBrowser($preferredBrowser);
        $subSession = $this->createSubSessionForSingleDriver($selectedDriver);
        $closure($subSession);
    }

    public function inOtherBrowsers(Closure $closure, string $excludedBrowser = null): void
    {
        $selectedDrivers = $this->getDriversExcludingBrowser($preferredBrowser);
        foreach ($selectedDrivers as $selectedDriver) {
            $subSession = $this->createSubSessionForSingleDriver($selectedDriver);
            $closure($subSession);
        }
    }

    public function get(string $url): void
    {
        foreach ($this->drivers as $driver) {
            $driver->get($url);
        }
    }

    /**
     * Attention! Firefox always sets secure to true, whereas chrome respects the cookie settings.
     */
    public function setCookie(Cookie $cookie): void
    {
        foreach ($this->drivers as $driver) {
            $driver->manage()->addCookie($cookie);
        }
    }

    public function deleteCookie(Cookie|string $cookieOrName): void
    {
        if ($cookieOrName instanceof Cookie) {
            $cookieOrName = $cookieOrName->getName();
        }
        foreach ($this->drivers as $driver) {
            $driver->manage()->deleteCookieNamed($cookieOrName);
        }
    }

    /**
     * Shorthand for <code class="code">$session->findElement($selector)->click();</code>
     */
    public function click(string|WebDriverBy $linkTextOrSelector): void
    {
        if (is_string($linkTextOrSelector)) {
            $linkTextOrSelector = WebDriverBy::linkText($linkTextOrSelector);
        }
        foreach ($this->drivers as $driver) {
            $driver->findElement($linkTextOrSelector)->click();
        }
    }

    /**
     * Shorthand for <code class="code">$session->findElement($selector)->submit();</code>
     */
    public function submit(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->submit();
        }
    }

    public function fillField(WebDriverBy $selector, string $string): void
    {
        foreach ($this->drivers as $driver) {
            $element = $driver->findElement($selector);
            try {
                $element->clear()->sendKeys($string);
            } catch (ElementNotInteractableException $exception) {
                $tagName = $element->getTagName();
                if ('input' === $tagName) {
                    $type = $element->getAttribute('type');
                    $hidden = $element->getAttribute('hidden');
                    if ('hidden' === $type || 'true' === $hidden) {
                        throw new HiddenInputCanNotBeFilledException($element, $exception);
                    }
                }
                throw $exception;
            }
        }
    }

    public function fillHiddenField(WebDriverBy $selector, string $string): void
    {
        $script = <<<JS
function fillHiddenInput(element, value) {
    element.value = value
}
return (fillHiddenInput).apply(null, arguments);
JS;
        foreach ($this->drivers as $driver) {
            $element = $driver->findElement($selector);
            $driver->executeScript($script, [$element, $string]);
        }
    }

    public function clearField(WebDriverBy $selector): void
    {
        foreach ($this->drivers as $driver) {
            $driver->findElement($selector)->clear();
        }
    }

    public function selectOption(WebDriverBy $selector, WebDriverBy|string $option): void
    {
        foreach ($this->drivers as $driver) {
            $selectElement = $driver->findElement($selector);
            $select = new WebDriverSelect($selectElement);
            if (is_string($option)) {
                $select->selectByVisibleText($option);
            } else {
                $option = $selectElement->findElement($option);
                $select->selectByValue($option->getAttribute('value'));
            }
        }
    }

    public function getFormElement(WebDriverBy $selector): FormElement
    {
        $elements = [];
        foreach ($this->drivers as $browserName => $driver) {
            $element = $driver->findElement($selector);
            $webDriverFormElement = match ($element->getTagName()) {
                'select' => new WebDriverSelect($element),
                'input' => match ($element->getAttribute('type')) {
                    'check' => new WebDriverCheckboxes($element),
                    'radio' => new WebDriverRadios($element),
                },
            };
            $elements[$browserName] = $webDriverFormElement;
        }
        return AbstractSelectable::fromElements($elements);
    }

    public function getCheckboxes(WebDriverBy $selector): Checkboxes
    {
        $checkboxes = [];
        foreach ($this->drivers as $browserName => $driver) {
            $element = $driver->findElement($selector);
            $checkboxes[$browserName] = new WebDriverCheckboxes($element);
        }
        return new Checkboxes($checkboxes);
    }

    public function getRadios(WebDriverBy $selector): Radios
    {
        $radios = [];
        foreach ($this->drivers as $browserName => $driver) {
            $element = $driver->findElement($selector);
            $radios[$browserName] = new WebDriverRadios($element);
        }
        return new Radios($radios);
    }

    public function getSelect(WebDriverBy $selector): Select
    {
        $selects = [];
        foreach ($this->drivers as $browserName => $driver) {
            $element = $driver->findElement($selector);
            $selects[$browserName] = new WebDriverSelect($element);
        }
        return new Select($selects);
    }

    public function submitForm(WebDriverBy $formSelector, array $data = []): void
    {
        $this->fillForm($formSelector, $data);
        foreach ($this->drivers as $driver) {
            $formElement = $driver->findElement($formSelector);
            $formElement->submit();
        }
    }

    public function fillForm(WebDriverBy $formSelector, array $data): void
    {
        foreach ($this->drivers as $driver) {
            $formElement = $driver->findElement($formSelector);
            $form = new Form($formElement);
            $form->setData($data);
        }
    }

    public function findElement(WebDriverBy $selector): Element
    {
        $elementPerDriver = [];
        foreach ($this->drivers as $browserName => $driver) {
            $elementPerDriver[$browserName] = $driver->findElement($selector);
        }
        return new Element($elementPerDriver);
    }

    public function findElements(WebDriverBy $selector): Elements
    {
        $elementsPerDriver = [];
        foreach ($this->drivers as $browserName => $driver) {
            $elementsPerDriver[$browserName] = $driver->findElements($selector);
        }
        return new Elements($elementsPerDriver);
    }

    public function executeScript(string $javascript, array $arguments = []): void
    {
        foreach ($this->drivers as $driver) {
            $resolvedArguments = $this->resolveWebDriverByForDriver($driver, $arguments);
            $driver->executeScript($javascript, $resolvedArguments);
        }
    }

    public function executeAsyncScript(string $javascript, array $arguments = []): void
    {
        foreach ($this->drivers as $driver) {
            $resolvedArguments = $this->resolveWebDriverByForDriver($driver, $arguments);
            $driver->executeAsyncScript($javascript, $resolvedArguments);
        }
    }

    /**
     * Shorthand for <code class='code'>$driver->wait()->until($condition);</code>
     */
    public function waitUntil(Closure|WebDriverExpectedCondition $condition): void
    {
        foreach ($this->drivers as $driver) {
            $session = $this->createSubSessionForSingleDriver($driver);
            $wait = new SessionWait($session);
            $wait->until($condition);
        }
    }

    protected function resolveWebDriverByForDriver(RemoteWebDriver $driver, mixed $argument): mixed
    {
        if (is_array($argument)) {
            $resolved = [];
            foreach ($argument as $index => $value) {
                $resolved[$index] = $this->resolveWebDriverByForDriver($driver, $value);
            }
            return $resolved;
        }
        if ($argument instanceof WebDriverBy) {
            return $driver->findElement($argument);
        }
        return $argument;
    }

    public function __destruct()
    {
        foreach ($this->drivers as $driver) {
            $driver->quit();
        }
    }

    public function inPopupContext(Closure $closure): void
    {
        foreach ($this->drivers as $driver) {
            $subSession = $this->createSubSessionForSingleDriver($driver);
            $alert = $driver->switchTo()->alert();
            try {
                $closure($subSession, $alert);
            } finally {
                $driver->switchTo()->defaultContent();
            }
        }
    }

    public function refresh(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->navigate()->refresh();
        }
    }

    public function forward(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->navigate()->forward();
        }
    }

    public function back(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->navigate()->back();
        }
    }

    public function isInIFrameContext(): bool
    {
        $results = [];
        foreach ($this->drivers as $browserName => $driver) {
            $results[$browserName] = $driver->executeScript('return window.self !== window.top;');
        }
        if (in_array(true, $results, true) && in_array(false, $results, true)) {
            $msg = [];
            foreach ($results as $browserName => $result) {
                $msg[] = $browserName . '=' . ($result ? 'true' : 'false');
            }
            throw new Exception('Got different results for browsers: ' . implode(' ', $msg));
        }
        return in_array(true, $results, true);
    }

    public function inIFrameContext(WebDriverBy|WebDriverElement|null|int|string $frame, Closure $closure): void
    {
        foreach ($this->drivers as $driver) {
            $subSession = $this->createSubSessionForSingleDriver($driver);
            $resolvedFrame = $this->resolveWebDriverByForDriver($driver, $frame);
            try {
                $driver->switchTo()->frame($resolvedFrame);
                $closure($subSession);
            } finally {
                $driver->switchTo()->defaultContent();
            }
        }
    }

    public function createSubSessionForSingleDriver(RemoteWebDriver $driver): Session
    {
        return new SubSession($this, $driver);
    }

    /**
     * @internal You should always use the Session object. Use `createSubSessionForSingleDriver` instead.
     */
    public function getDriverForBrowser(?string $preferredBrowser = null): RemoteWebDriver
    {
        $selectedDriver = null;
        if (null !== $preferredBrowser) {
            $selectedDriver = $this->drivers[$preferredBrowser] ?? null;
        }
        if (null === $selectedDriver) {
            $key = array_key_first($this->drivers);
            $selectedDriver = $this->drivers[$key];
        }
        return $selectedDriver;
    }

    /**
     * @internal You should always use the Session object. Use `createSubSessionForSingleDriver` instead.
     */
    public function getDriversExcludingBrowser(?string $excludedBrowser = null): RemoteWebDriver
    {
        if (null === $excludedBrowser || !array_key_exists($excludedBrowser, $this->drivers)) {
            $excludedBrowser = array_key_first($this->drivers);
        }
        $drivers = $this->drivers;
        unset($drivers[$excludedBrowser]);
        return $drivers;
    }

    public function contextClickElement(Element $element): void
    {
        foreach ($element->elementPerDriver as $browserName => $driverElement) {
            $actions = $this->drivers[$browserName]->action();
            $actions->contextClick($driverElement);
            $actions->perform();
        }
    }

    public function actOnElement(Element $element, Closure $closure): void
    {
        foreach ($element->elementPerDriver as $browserName => $driverElement) {
            $driver = $this->drivers[$browserName];
            $subSession = $this->createSubSessionForSingleDriver($driver);
            $closure($subSession, $driverElement);
        }
    }
}
