<?php

declare(strict_types=1);

namespace CoStack\StackTest\Recorder;

use CoStack\StackTest\Decorator\WebDriverDecorator;
use CoStack\StackTest\Pattern\Singleton;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use WeakReference;

class WebDriverRecorder
{
    use Singleton;

    protected const LIMIT = 100;
    protected array $calls = [];
    protected array $history = [];

    public function record(): void
    {
        $frame = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1];

        /** @var WebDriverDecorator $decorator */
        $decorator = $frame['object'];

        $driver = WeakReference::create($decorator->inner);
        $this->calls[] = [
            'driver' => $driver,
            'func' => $frame['function'],
            'args' => $frame['args'],
        ];
        if ($frame['function'] === 'get') {
            $this->history[spl_object_hash($decorator->inner)][] = $frame['args'][0];
        }
        $toRemove = count($this->calls) - self::LIMIT;
        reset($this->calls);
        while ($toRemove > 0) {
            $key = key($this->calls);
            next($this->calls);
            unset($this->calls[$key]);
            --$toRemove;
        }
    }

    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getHistory(RemoteWebDriver $driver): array
    {
        return $this->history[spl_object_hash($driver)] ?? [];
    }
}
