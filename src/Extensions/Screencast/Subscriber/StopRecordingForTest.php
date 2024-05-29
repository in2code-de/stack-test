<?php

declare(strict_types=1);

namespace CoStack\StackTest\Extensions\Screencast\Subscriber;

use CoStack\StackTest\Extensions\Screencast\Screencast;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

class StopRecordingForTest implements FinishedSubscriber
{
    public function __construct(
        private readonly Screencast $screencast,
    ) {}

    public function notify(Finished $event): void
    {
        $this->screencast->stop($event);
    }
}
