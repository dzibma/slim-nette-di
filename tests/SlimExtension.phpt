<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$compiler = (new Nette\DI\Compiler())
    ->setClassName($class = uniqid('Container_'))
    ->addExtension('slim', new Dzibma\SlimNette\SlimExtension());

eval($compiler->compile());

/** @var Nette\DI\Container $container */
$container = new $class();

Assert::notNull($container->getByType(Psr\Http\Message\ResponseFactoryInterface::class, false));
Assert::notNull($container->getByType(Slim\Interfaces\CallableResolverInterface::class, false));

Assert::true($container->hasService('slim.app'));
/** @var Slim\App $app */
$app = $container->getByType(Slim\App::class, false);
Assert::notNull($app);

Assert::type(Psr\Container\ContainerInterface::class, $app->getContainer());
