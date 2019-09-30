# Dzibma\SlimNette

[Slim](https://github.com/slimphp/Slim) + [Nette DI container](https://github.com/nette/di)

## Installation
Requires PHP >= 7.1

### Install SlimNette via composer
```bash
composer req dzibma/slim-nette
```

### Install PSR-7 implementation & ServerRequest factory
```bash
composer req slim/psr7
```
You can choose any of the folowing implementations
* [slim/psr7](https://github.com/slimphp/Slim-Psr7)
* [nyholm/psr7](https://github.com/Nyholm/psr7) + [nyholm/psr7-server](https://github.com/Nyholm/psr7-server)
* [guzzlehttp/psr7](https://github.com/guzzle/psr7) + [http-interop/http-factory-guzzle](https://github.com/http-interop/http-factory-guzzle)
* [zendframework/zend-diactoros](https://github.com/zendframework/zend-diactoros)

## Bootstrap Slim app
```php
<?php

use Dzibma\SlimNette\ContainerBuilder;

require __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->setTempDir(__DIR__ . '/../cache');
$builder->addConfig(__DIR__ . '/config/common.neon');
$builder->setDebugMode(true);

$container = $builder->build();

/** @var Slim\App $app */
$app = $container->getByType(Slim\App::class);

// add middlewares
// add routes

$app->run();
```
