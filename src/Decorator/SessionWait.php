<?php

declare(strict_types=1);

namespace CoStack\StackTest\Decorator;

use CoStack\StackTest\Session\Session;
use Facebook\WebDriver\WebDriverWait;

/**
 * @property Session $driver
 */
class SessionWait extends WebDriverWait
{
    /**
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     */
    public function __construct(Session $driver, $timeout_in_second = null, $interval_in_millisecond = null)
    {
        $this->driver = $driver;
        $this->timeout = $timeout_in_second ?? 30;
        $this->interval = $interval_in_millisecond ?: 250;
    }
}
