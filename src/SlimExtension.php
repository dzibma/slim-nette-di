<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

use Nette\DI\CompilerExtension;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;
use Slim\CallableResolver;
use Slim\Factory\AppFactory;
use Slim\Interfaces\CallableResolverInterface;

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

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        if ($builder->getByType(ResponseFactoryInterface::class) === null) {
            $builder->addDefinition(null)
                ->setClass(ResponseFactoryInterface::class)
                ->setFactory([AppFactory::class, 'determineResponseFactory']);
        }

        if ($builder->getByType(CallableResolverInterface::class) === null) {
            $builder->addDefinition(null)
                ->setClass(CallableResolver::class);
        } 
    }
}
