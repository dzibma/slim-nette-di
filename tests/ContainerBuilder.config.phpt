<?php

declare(strict_types=1);

use Tester\Assert;
use Tester\FileMock;

require __DIR__ . '/bootstrap.php';

date_default_timezone_set('America/Los_Angeles');

$config = <<<EOT
parameters:
    foo: bar
services:
    foo: FooMockService
    bar:
        factory: BarMockService
        inject: on
        tags: [bar]
constants:
    FOOBAR: foobar
EOT;

$builder = new Dzibma\SlimNette\ContainerBuilder();
$builder->addConfig(FileMock::create($config, 'neon'));
$builder->addConfig([
    'php' => [
        'date.timezone' => 'Europe/Prague',
    ],
]);

$container = $builder->build();

Assert::equal([
    'debugMode' => false,
    'consoleMode' => PHP_SAPI === 'cli',
    'foo' => 'bar',
], $container->getParameters());

// DecoratorExtension
Assert::count(1, $container->findByTag('bar'));

/** @var BarMockService $bar */
$bar = $container->getByType(BarMockService::class);
// InjectExtension
Assert::type(FooMockService::class, $bar->foo);

// ConstantsExtension
Assert::true(defined('FOOBAR'));
Assert::same('foobar', FOOBAR);

// PhpExtension
Assert::same('Europe/Prague', date_default_timezone_get());
