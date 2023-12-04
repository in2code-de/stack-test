<?php

declare(strict_types=1);

namespace CoStack\StackTest\WebDriver\Remote;

use Closure;
use Exception;
use Facebook\WebDriver\Remote\RemoteExecuteMethod;
use Facebook\WebDriver\Remote\ShadowRoot;
use Facebook\WebDriver\WebDriverBy;

class MultiShadowRoot extends ShadowRoot
{
    /**
     * @param array<ShadowRoot> $shadowRoots
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(public readonly array $shadowRoots)
    {
    }

    public static function createFromResponse(RemoteExecuteMethod $executor, array $response): never
    {
        throw new Exception('MultiShadowRoot can only be created by calling the constructor');
    }

    public function findElement(WebDriverBy $locator)
    {
        $elements = [];
        foreach ($this->shadowRoots as $shadowRoot) {
            $elements[] = $shadowRoot->findElement($locator);
        }
        return new MultiRemoteWebElement($elements);
    }

    public function findElements(WebDriverBy $locator)
    {
        $results = [];
        foreach ($this->shadowRoots as $outerIndex => $shadowRoot) {
            $elements = $shadowRoot->findElements($locator);
            foreach ($elements as $innerIndex => $element) {
                $results[$innerIndex][$outerIndex] = $element;
            }
        }
        foreach ($results as $index => $elements) {
            $results[$index] = new MultiRemoteWebElement($elements);
        }
        return $results;
    }

    public function getID()
    {
        throw new Exception(
            'Can not call method with different return values for each browser. Use foreachID instead.',
        );
    }

    public function foreachID(Closure $callback): void
    {
        foreach ($this->shadowRoots as $shadowRoot) {
            $callback($shadowRoot->getID());
        }
    }
}
