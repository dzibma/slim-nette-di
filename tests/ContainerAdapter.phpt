<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$compiler = (new Nette\DI\Compiler())
    ->setClassName($class = uniqid('Container_'))
    ->addConfig(['services' => ['foo' => FooMockService::class, SickMockService::class]]);

eval($compiler->compile());

$container = new $class();
$containerAdapter = new Dzibma\SlimNette\ContainerAdapter($container);

Assert::true($containerAdapter->has('foo'));

Assert::type(
    FooMockService::class,
    $containerAdapter->get('foo')
);

Assert::same(
    $container->getService('foo'),
    $containerAdapter->get('foo')
);

Assert::same(
    $container->getByType(FooMockService::class),
    $containerAdapter->get(FooMockService::class)
);

Assert::false($containerAdapter->has('bar'));

Assert::throws(
    function () use ($containerAdapter) {
        $containerAdapter->get('bar');
    }, 
    Dzibma\SlimNette\ServiceNotFoundException::class
);

Assert::throws(
    function () use ($containerAdapter) {
        $containerAdapter->get(SickMockService::class);
    },
    Dzibma\SlimNette\ContainerException::class
);

Assert::throws(
    function () use ($containerAdapter) {
        $containerAdapter->has(SickMockService::class);
    },
    Dzibma\SlimNette\ContainerException::class
);
