<?php

declare(strict_types=1);

namespace CoStack\StackTest\Pattern;

trait Singleton
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }
}
