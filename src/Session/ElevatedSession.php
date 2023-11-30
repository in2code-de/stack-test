<?php

declare(strict_types=1);

namespace CoStack\StackTest\Session;

use Facebook\WebDriver\Remote\RemoteWebDriver;

use function uniqid;

class ElevatedSession extends Session
{
    public function __construct(RemoteWebDriver $driver)
    {
        $browserName = $driver->getCapabilities()->getBrowserName();
        /** @noinspection PhpRedundantOptionalArgumentInspection */
        parent::__construct(uniqid('elevated-', false) . '-' . $browserName, [$driver]);
    }

    /**
     * @noinspection MagicMethodsValidityInspection
     */
    public function __destruct()
    {
        // Never close browsers of elevated sessions
    }
}
