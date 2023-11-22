<?php

declare(strict_types=1);

namespace CoStack\StackTest\Iterator;

use RecursiveFilterIterator;
use SplFileInfo;

class RecursiveTestFileFilterIterator extends RecursiveFilterIterator
{
    public function accept(): bool
    {
        /** @var SplFileInfo $current */
        $current = parent::current();
        return str_ends_with($current->getFilename(), 'Test.php');
    }
}
