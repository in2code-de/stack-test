<?php

declare(strict_types=1);

namespace CoStack\StackTest\Recorder;

use CoStack\StackTest\Pattern\Singleton;
use CoStack\StackTest\WebDriver\Remote\WebDriver;
use WeakReference;

class WebDriverRecorder
{
    use Singleton;

    protected const LIMIT = 100;
    protected array $calls = [];
    protected array $history = [];

    public function record(): void
    {
        $frame = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4)[3];

        /** @var WebDriver $driver */
        $driver = $frame['object'];

        $driver = WeakReference::create($driver);
        $this->calls[] = [
            'driver' => $driver,
            'func' => $frame['function'],
            'args' => $frame['args'],
        ];
        if ($frame['function'] === 'get') {
            $this->history[spl_object_hash($driver)][] = $frame['args'][0];
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

    public function getHistory(WebDriver $driver): array
    {
        return $this->history[spl_object_hash($driver)] ?? [];
    }
}
