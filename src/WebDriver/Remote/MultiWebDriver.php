<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Closure;
use CoStack\StackTest\Test\Constraint\Visibility\ElementIsNotVisible;
use Exception;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteKeyboard;
use Facebook\WebDriver\Remote\RemoteMouse;
use Facebook\WebDriver\Remote\RemoteTouchScreen;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\WebDriverResponse;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCommandExecutor;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverWait;

use function array_values;
use function count;
use function current;
use function func_get_args;
use function next;
use function reset;

class MultiWebDriver extends WebDriver
{
    /**
     * @param array<WebDriver> $drivers
     * @noinspection MagicMethodsValidityInspection
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(public readonly string $sessionId, public readonly array $drivers)
    {
    }

    public static function create(
        $selenium_server_url = 'http://localhost:4444/wd/hub',
        $desired_capabilities = null,
        $connection_timeout_in_ms = null,
        $request_timeout_in_ms = null,
        $http_proxy = null,
        $http_proxy_port = null,
        DesiredCapabilities $required_capabilities = null,
    ): never {
        throw new Exception('MultiWebDriver can only be created by calling the constructor');
    }

    public static function createBySessionID(
        $session_id,
        $selenium_server_url = 'http://localhost:4444/wd/hub',
        $connection_timeout_in_ms = null,
        $request_timeout_in_ms = null,
    ): never {
        throw new Exception('MultiWebDriver can only be created by calling the constructor');
    }

    public function reset(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->reset();
        }
    }

    public function submit(WebDriverBy $by): static
    {
        foreach ($this->drivers as $driver) {
            $driver->submit($by);
        }
        return $this;
    }

    public function submitForm(WebDriverBy $by, array $data = []): void
    {
        foreach ($this->drivers as $driver) {
            $driver->submitForm($by, $data);
        }
    }

    public function fillForm(WebDriverBy $by, array $data): void
    {
        foreach ($this->drivers as $driver) {
            $driver->fillForm($by, $data);
        }
    }

    public function fillField(WebDriverBy $by, string $string): static
    {
        foreach ($this->drivers as $driver) {
            $driver->fillField($by, $string);
        }
        return $this;
    }

    public function clearField(WebDriverBy $by): static
    {
        foreach ($this->drivers as $driver) {
            $driver->clearField($by);
        }
        return $this;
    }

    public function click(WebDriverBy|string $by): static
    {
        foreach ($this->drivers as $driver) {
            $driver->click($by);
        }
        return $this;
    }

    public function contextClick(WebDriverBy|RemoteWebElement $by): static
    {
        if ($by instanceof MultiRemoteWebElement) {
            $elements = array_values($by->elements);
            $drivers = array_values($this->drivers);
            if (count($elements) !== count($drivers)) {
                throw new Exception('Mismatch of elements and drivers');
            }
            foreach ($elements as $index => $element) {
                $drivers[$index]->contextClick($element);
            }
            return $this;
        }
        foreach ($this->drivers as $driver) {
            $driver->contextClick($by);
        }
        return $this;
    }

    public function isInIFrameContext(): bool
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function close(): static
    {
        foreach ($this->drivers as $driver) {
            $driver->close();
        }
        return $this;
    }

    public function getFirstDriver(): WebDriver
    {
        $firstKey = array_key_first($this->drivers);
        return $this->drivers[$firstKey];
    }

    public function inFirstDriver(Closure $callback): mixed
    {
        return $callback($this->getFirstDriver());
    }

    /** @return array<WebDriver> */
    public function getDriversExceptFirst(): array
    {
        $firstKey = array_key_first($this->drivers);
        $drivers = $this->drivers;
        unset($this->drivers[$firstKey]);
        return $drivers;
    }

    public function inIFrameContext(WebDriverBy|WebDriverElement|null|int|string $frame, Closure $callback): void
    {
        foreach ($this->drivers as $driver) {
            $driver->inIFrameContext($frame, $callback);
        }
    }

    /**
     * @deprecated Use $driver->switchTo()->newWindow()
     */
    public function newWindow(): static
    {
        foreach ($this->drivers as $driver) {
            $driver->newWindow();
        }
        return $this;
    }

    public function findElement(WebDriverBy $by): MultiRemoteWebElement
    {
        $result = [];
        foreach ($this->drivers as $driver) {
            $result[$driver->browserName] = $driver->findElement($by);
        }
        return new MultiRemoteWebElement($result);
    }

    /** @return array<MultiRemoteWebElement> */
    public function findElements(WebDriverBy $by): array
    {
        $result = [];
        foreach ($this->drivers as $outerIndex => $driver) {
            $elements = $driver->findElements($by);
            foreach ($elements as $innerIndex => $element) {
                $result[$innerIndex][$outerIndex] = $element;
            }
        }
        foreach ($result as $index => $elements) {
            $result[$index] = new MultiRemoteWebElement($elements);
        }
        return $result;
    }

    public function get($url): static
    {
        foreach ($this->drivers as $driver) {
            $driver->get($url);
        }
        return $this;
    }

    public function getPageSource(): string
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function getTitle(): string
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function getWindowHandle(): string
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function getWindowHandles(): array
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    public function quit(): void
    {
        foreach ($this->drivers as $driver) {
            $driver->quit();
        }
    }

    public function executeScript($script, array $arguments = []): mixed
    {
        return $this->getReturnValue(__FUNCTION__, func_get_args());
    }

    public function executeAsyncScript($script, array $arguments = []): mixed
    {
        return $this->getReturnValue(__FUNCTION__, func_get_args());
    }

    public function takeScreenshot($save_as = null): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachScreenshot instead.',
        );
    }

    public function foreachScreenshot(Closure $callback, $save_as = null): void
    {
        foreach ($this->drivers as $driver) {
            $screenshot = $driver->takeScreenshot($save_as);
            $callback($screenshot);
        }
    }

    public function getStatus(): never
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachStatus instead.',
        );
    }

    public function foreachStatus(Closure $callback): void
    {
        foreach ($this->drivers as $driver) {
            $callback($driver->getStatus());
        }
    }

    public function wait($timeout_in_second = 30, $interval_in_millisecond = 250): WebDriverWait
    {
        return parent::wait($timeout_in_second, $interval_in_millisecond);
    }

    public function manage(): MultiWebDriverOptions
    {
        return new MultiWebDriverOptions($this);
    }

    public function navigate(): MultiWebDriverNavigation
    {
        return new MultiWebDriverNavigation($this);
    }

    public function switchTo(): MultiRemoteTargetLocator
    {
        return new MultiRemoteTargetLocator($this);
    }

    public function getMouse(): RemoteMouse
    {
        return parent::getMouse();
    }

    public function getKeyboard(): RemoteKeyboard
    {
        return parent::getKeyboard();
    }

    public function getTouch(): RemoteTouchScreen
    {
        return parent::getTouch();
    }

    public function action(): WebDriverActions
    {
        return parent::action();
    }

    public function setCommandExecutor(WebDriverCommandExecutor $executor): never
    {
        throw new Exception('Calling setCommandExecutor is not allowed');
    }

    public function getCommandExecutor(): never
    {
        throw new Exception('Calling getCommandExecutor is not allowed');
    }

    public function setSessionID($session_id): never
    {
        throw new Exception('Calling setSessionID is not allowed');
    }

    public function getSessionID(): never
    {
        throw new Exception('Calling getSessionID is not allowed');
    }

    public function getCapabilities(): never
    {
        throw new Exception('Calling getCapabilities is not allowed');
    }

    public static function getAllSessions(
        $selenium_server_url = 'http://localhost:4444/wd/hub',
        $timeout_in_ms = 30000,
    ): never {
        throw new Exception('Calling getAllSessions is not allowed');
    }

    public function execute($command_name, $params = []): never
    {
        throw new Exception('Calling execute is not allowed');
    }

    public function executeCustomCommand($endpointUrl, $method = 'GET', $params = []): never
    {
        throw new Exception('Calling executeCustomCommand is not allowed');
    }

    public function isW3cCompliant()
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    protected static function createFromResponse(
        WebDriverResponse $response,
        HttpCommandExecutor $commandExecutor,
    ): never {
        throw new Exception('Calling createFromResponse is not allowed');
    }

    protected function prepareScriptArguments(array $arguments): never
    {
        throw new Exception('Calling prepareScriptArguments is not allowed');
    }

    protected function getExecuteMethod(): never
    {
        throw new Exception('Calling getExecuteMethod is not allowed');
    }

    protected function newElement($id): never
    {
        throw new Exception('Calling newElement is not allowed');
    }

    protected static function castToDesiredCapabilitiesObject($desired_capabilities = null): never
    {
        throw new Exception('Calling castToDesiredCapabilitiesObject is not allowed');
    }

    protected static function readExistingCapabilitiesFromSeleniumGrid(
        string $session_id,
        HttpCommandExecutor $executor,
    ): never {
        throw new Exception('Calling readExistingCapabilitiesFromSeleniumGrid is not allowed');
    }

    public function getCurrentURL(): string
    {
        return $this->getReturnValue(__FUNCTION__);
    }

    /**
     * @throws DifferentValuesException
     */
    protected function getReturnValue(string $method, array $arguments = []): mixed
    {
        $drivers = $this->drivers;
        $initial = reset($drivers)->{$method}(...$arguments);
        next($drivers);
        $values = [];
        while ($driver = current($drivers)) {
            next($drivers);
            $pageSource = $driver->{$method}(...$arguments);
            if ($pageSource !== $initial) {
                $values[$driver->browserName] = $pageSource;
            }
        }
        if (!empty($values)) {
            throw new DifferentValuesException($initial, $values);
        }
        return $initial;
    }
}
