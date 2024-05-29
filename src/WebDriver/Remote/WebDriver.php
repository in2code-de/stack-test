<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Closure;
use CoStack\StackTest\Elements\Single\Form;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Exception\InvalidSessionIdException;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCapabilities;
use Facebook\WebDriver\WebDriverElement;

use function is_string;

class WebDriver extends RemoteWebDriver
{
    public readonly string $browserName;

    public function __construct(
        HttpCommandExecutor $commandExecutor,
        $sessionId,
        WebDriverCapabilities $capabilities,
        $isW3cCompliant = false,
    ) {
        // Shutdown functions will close the session even if the process was terminated
        register_shutdown_function(fn() => $this->quit());
        parent::__construct(new RecordingCommandExecutor($commandExecutor), $sessionId, $capabilities, $isW3cCompliant);
        $this->browserName = $this->capabilities->getBrowserName();
        $this->manage()->window()->maximize();
    }

    public function __destruct()
    {
        $this->quit();
    }

    public function close(): static
    {
        // Closing implicitly quits the session, but has shown to be problematic when reopening the session.
        // Quitting is more stable than closing.
        // https://developer.mozilla.org/en-US/docs/Web/WebDriver/Errors/InvalidSessionID#implicit_session_deletion
        $this->quit();
        return $this;
    }

    public function quit(): void
    {
        try {
            parent::quit();
        } catch (InvalidSessionIdException) {
        }
    }

    public function submit(WebDriverBy $by): static
    {
        $this->findElement($by)->submit();
        return $this;
    }

    public function submitForm(WebDriverBy $by, array $data = []): void
    {
        $this->fillForm($by, $data);
        $formElement = $this->findElement($by);
        $formElement->submit();
    }

    public function fillForm(WebDriverBy $by, array $data): void
    {
        $form = new Form($this, $by);
        $form->setData($data);
    }

    public function fillField(WebDriverBy $by, string $string): static
    {
        $element = $this->findElement($by);
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
        return $this;
    }

    public function clearField(WebDriverBy $by): static
    {
        $this->findElement($by)->clear();
        return $this;
    }

    public function click(WebDriverBy|string $by): static
    {
        if (is_string($by)) {
            $by = WebDriverBy::linkText($by);
        }
        $element = $this->findElement($by);
        $element->click();
        return $this;
    }

    public function contextClick(WebDriverBy|RemoteWebElement $element): static
    {
        if ($element instanceof WebDriverBy) {
            $element = $this->findElement($element);
        }
        $action = $this->action();
        $action->contextClick($element);
        $action->perform();
        return $this;
    }

    public function inIFrameContext(WebDriverBy|WebDriverElement|null|int|string $frame, Closure $callback): void
    {
        try {
            if ($frame instanceof WebDriverBy) {
                $frame = $this->findElement($frame);
            }
            $this->switchTo()->frame($frame);
            $callback($this);
        } finally {
            $this->switchTo()->defaultContent();
        }
    }

    public function isInIFrameContext(): bool
    {
        return $this->executeScript('return window.self !== window.top;');
    }

    public function getCurrentURL(): string
    {
        return $this->executeScript('return window.location.href;');
    }
}
