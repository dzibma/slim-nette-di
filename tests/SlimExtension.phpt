<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$compiler = new Nette\DI\Compiler();
$compiler->setClassName($class = uniqid('Container_'));
$compiler->addExtension('slim', new Dzibma\SlimNette\SlimExtension());
eval($compiler->compile());

/** @var Nette\DI\Container $container */
$container = new $class();

Assert::true($container->hasService('slim.app'));
/** @var Slim\App $app */
$app = $container->getByType(Slim\App::class, false);
Assert::notNull($app);

Assert::type(Psr\Container\ContainerInterface::class, $app->getContainer());
