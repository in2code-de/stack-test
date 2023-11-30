<?php

declare(strict_types=1);

namespace CoStack\StackTest\Session;

use Facebook\WebDriver\Remote\RemoteWebDriver;

use function uniqid;

class SubSession extends Session
{
    public function __construct(protected readonly Session $parent, RemoteWebDriver $driver)
    {
        $browserName = $driver->getCapabilities()->getBrowserName();
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        parent::__construct(uniqid('subsession-', false) . '-' . $parent->sessionId . '-' . $browserName, [$driver]);
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public function __destruct()
    {
        // Never close browsers of sub-sessions
    }
}
