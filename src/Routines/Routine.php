<?php

declare(strict_types=1);

namespace CoStack\StackTest\Routines;

use Facebook\WebDriver\Remote\RemoteWebDriver;

interface Routine
{
    public function execute(RemoteWebDriver $driver): void;
}
