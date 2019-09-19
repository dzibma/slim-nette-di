<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

use Nette\DI\CompilerExtension;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimExtension extends CompilerExtension
{
    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('container'))
            ->setFactory(ContainerAdapter::class)
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('app'))
            ->setClass(App::class)
            ->setFactory([AppFactory::class, 'createFromContainer'], [$this->prefix('@container')]);
    }
}
