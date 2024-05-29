<?php

declare(strict_types=1);

namespace CoStack\StackTest\Extensions\Screencast\Subscriber;

use CoStack\StackTest\Extensions\Screencast\Screencast;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

class StartRecordingForTest implements PreparedSubscriber
{
    public function __construct(
        private readonly Screencast $screencast,
    ) {}

    public function notify(Prepared $event): void
    {
        $this->screencast->start($event);
    }
}
