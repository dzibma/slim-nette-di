<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/bootstrap.php';

$container = (new Dzibma\SlimNette\ContainerBuilder())
    ->setDebugMode(true)
    ->build();

$params = $container->getParameters();
Assert::true($params['debugMode'] ?? null);
