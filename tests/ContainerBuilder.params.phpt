<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$builder = new Dzibma\SlimNette\ContainerBuilder();

$builder->addParameters([
    'bar' => [
        'a' => 1,
        'c' => 3,
    ],
]);

$builder->addDynamicParameters([
    'foo' => 'foobar',
]);

$builder->addParameters([
    'foo' => 0,
    'bar' => [
        'b' => 2,
    ],
]);

$container = $builder->build();

Assert::equal([
    'foo' => 'foobar',
    'bar' => [
        'a' => 1,
        'b' => 2,
        'c' => 3,
    ],
    //defaults:
    'debugMode' => false,
    'consoleMode' => PHP_SAPI === 'cli',
], $container->getParameters());
