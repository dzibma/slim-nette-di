<?php

declare(strict_types=1);

use Tester\Assert;
use Tester\Helpers;

require __DIR__ . '/bootstrap.php';

$tempDir =  __DIR__ . '/tmp';
Helpers::purge($tempDir);

$container = (new Dzibma\SlimNette\ContainerBuilder())
    ->setTempDir($tempDir)
    ->build();

$params = $container->getParameters();
Assert::same($tempDir, $params['tempDir'] ?? null);

$filename = (new ReflectionClass($container))->getFileName();
Assert::true(is_file($filename));
