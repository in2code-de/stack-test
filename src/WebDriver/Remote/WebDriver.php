<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use CoStack\StackTest\Elements\Single\Form;
use CoStack\StackTest\Exception\HiddenInputCanNotBeFilledException;
use CoStack\StackTest\Routines\Reset\ChromeReset;
use CoStack\StackTest\Routines\Reset\FirefoxReset;
use Exception;
use Facebook\WebDriver\Exception\ElementNotInteractableException;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverBrowserType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverCapabilities;

class WebDriver extends RemoteWebDriver
{
    public readonly string $browserName;

    public function __construct(
        HttpCommandExecutor $commandExecutor,
        $sessionId,
        WebDriverCapabilities $capabilities,
        $isW3cCompliant = false,
    ) {
        parent::__construct(new RecordingCommandExecutor($commandExecutor), $sessionId, $capabilities, $isW3cCompliant);
        $this->browserName = $this->capabilities->getBrowserName();
        $this->manage()->window()->maximize();
    }

    public function reset(): void
    {
        $routine = match ($this->browserName) {
            WebDriverBrowserType::FIREFOX => new FirefoxReset(),
            WebDriverBrowserType::CHROME => new ChromeReset(),
            default => throw new Exception('Reset routine not implemented for ' . $this->browserName),
        };
        $routine->execute($this);
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

    public function isInIFrameContext(): bool
    {
        return $this->executeScript('return window.self !== window.top;');
    }

    public function getCurrentURL(): string
    {
        return $this->executeScript('return window.location.href;');
    }
}
