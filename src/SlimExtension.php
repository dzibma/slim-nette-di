<?php

namespace Dzibma\Slim;

use Nette\DI\CompilerExtension;
use Nette\DI\Extensions\InjectExtension;
use Nette\DI\Statement;
use Nette\PhpGenerator\PhpLiteral;

class SlimExtension extends CompilerExtension
{
    private $settings = [
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
        'addContentLengthHeader' => true,
        'routerCacheFile' => false
    ];

    public function __construct($debugMode = false)
    {
        $this->settings['displayErrorDetails'] =  (bool) $debugMode;
    }

    public function loadConfiguration()
    {
        $builder = $this->getContainerBuilder();
        $settings = $this->validateConfig($this->settings);

        $builder->addDefinition($this->prefix('container'))
            ->setFactory(\Dzibma\Slim\ContainerAdapter::class)
            ->setAutowired(false);

        $builder->addDefinition($this->prefix('application'))
            ->setFactory(\Slim\App::class, [$this->prefix('@container')]);

        $builder->addDefinition($this->prefix('settings'))
            ->setFactory(\ArrayObject::class, [$settings])
            ->setAutowired(false);
        $builder->addAlias('settings', $this->prefix('settings'));

        $builder->addDefinition($this->prefix('environment'))
            ->setFactory(\Slim\Http\Environment::class, [$builder->literal('$_SERVER')])
            ->setAutowired(false);
        $builder->addAlias('environment', $this->prefix('environment'));

        $builder->addDefinition($this->prefix('request'))
            ->setFactory([\Slim\Http\Request::class, 'createFromEnvironment'], [$this->prefix('@environment')])
            ->setAutowired(false);
        $builder->addAlias('request', $this->prefix('request'));

        $builder->addDefinition($this->prefix('response'))
            ->setFactory([
                    new Statement(\Slim\Http\Response::class, [
                        200, new Statement(\Slim\Http\Headers::class, [['Content-Type' => 'text/html; charset=UTF-8']])
                    ]),
                    'withProtocolVersion'
                ], [$settings['httpVersion']
            ])
            ->setAutowired(false);
        $builder->addAlias('response', $this->prefix('response'));

        $builder->addDefinition($this->prefix('router'))
            ->setFactory(\Slim\Router::class)
            ->addSetup('setCacheFile', [$settings['routerCacheFile']])
            ->addSetup('setContainer', [$this->prefix('@container')])
            ->setAutowired(false);
        $builder->addAlias('router', $this->prefix('router'));

        $builder->addDefinition($this->prefix('foundHandler'))
            ->setFactory(\Slim\Handlers\Strategies\RequestResponse::class)
            ->setAutowired(false);
        $builder->addAlias('foundHandler', $this->prefix('foundHandler'));

        $builder->addDefinition($this->prefix('phpErrorHandler'))
            ->setFactory(\Slim\Handlers\PhpError::class, [$settings['displayErrorDetails']])
            ->setAutowired(false);
        $builder->addAlias('phpErrorHandler', $this->prefix('phpErrorHandler'));

        $builder->addDefinition($this->prefix('errorHandler'))
            ->setFactory(\Slim\Handlers\Error::class, [$settings['displayErrorDetails']])
            ->setAutowired(false);
        $builder->addAlias('errorHandler', $this->prefix('errorHandler'));

        $builder->addDefinition($this->prefix('notFoundHandler'))
            ->setFactory(\Slim\Handlers\NotFound::class)
            ->setAutowired(false);
        $builder->addAlias('notFoundHandler', $this->prefix('notFoundHandler'));

        $builder->addDefinition($this->prefix('notAllowedHandler'))
            ->setFactory(\Slim\Handlers\NotAllowed::class)
            ->setAutowired(false);
        $builder->addAlias('notAllowedHandler', $this->prefix('notAllowedHandler'));

        $builder->addDefinition($this->prefix('callableResolver'))
            ->setFactory(\Slim\CallableResolver::class, [$this->prefix('@container')])
            ->setAutowired(false);
        $builder->addAlias('callableResolver', $this->prefix('callableResolver'));
    }
}
