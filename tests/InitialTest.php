<?php

declare(strict_types=1);

namespace CoStack\StackTest\Tests;

use CoStack\StackTest\TestCase;

class InitialTest extends TestCase
{
    public function testOpeningUrl(): void
    {
        $session = $this->newSession();
        $session->get('https://web.local.co-stack-test.com/test.php');
        $session->see('hi there');

        $secondSession = $this->newSession();
        $secondSession->get('https://web.local.co-stack-test.com/test2.php');
        $secondSession->see('PHP Version 8.1');
    }
}
