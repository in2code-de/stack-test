<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Test\Assert\SessionAssertions;
use PHPUnit\Framework\TestCase;

abstract class BrowserTestCase extends TestCase
{
    use SessionAssertions;
}
