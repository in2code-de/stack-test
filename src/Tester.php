<?php

declare(strict_types=1);

namespace CoStack\StackTest;

use CoStack\StackTest\Iterator\RecursiveTestFileFilterIterator;

use RecursiveDirectoryIterator;

use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionMethod;

class Tester
{
    public function run(): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveTestFileFilterIterator(
                new RecursiveDirectoryIterator(
                    __DIR__ . '/../tests',
                ),
            ),
        );
        $files = iterator_to_array($files);
        $declaredClasses = get_declared_classes();
        foreach ($files as $file) {
            require $file;
            $className = substr($file->getFilename(), 0, -4);
            $newDeclaredClasses = get_declared_classes();
            $newClasses = array_diff($newDeclaredClasses, $declaredClasses);
            foreach ($newClasses as $newClass) {
                if (str_ends_with($newClass, $className)) {
                    $reflection = new ReflectionClass($newClass);
                    $parentClass = $reflection->getParentClass();
                    if ($parentClass && $parentClass->getName() === TestCase::class) {
                        $test = new $newClass();
                        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                            if (str_starts_with($method->getName(), 'test')) {
                                $test->{$method->getName()}();
                            }
                        }
                    }
                }
            }
        }
    }
}
