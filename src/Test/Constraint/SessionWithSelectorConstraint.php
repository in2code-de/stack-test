<?php

declare(strict_types=1);

namespace CoStack\StackTest\Test\Constraint;

use CoStack\StackTest\Session;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

abstract class SessionWithSelectorConstraint extends SessionConstrain
{
    public function __construct(
        RemoteWebDriver|Session $session,
        protected readonly WebDriverBy $selector,
    ) {
        parent::__construct($session);
    }
}
