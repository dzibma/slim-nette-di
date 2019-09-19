<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

use Nette\DI\Compiler;
use Nette\DI\Config\Helpers;
use Nette\DI\Config\Loader;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;

class ContainerBuilder
{
    /** @var array */
    private $configs = [];

    /** @var array */
    private $parameters = [
        'consoleMode' => PHP_SAPI === 'cli',
        'debugMode' => false,
    ];

    /** @var array */
    private $dynamicParameters = [];

    public function setTempDir(string $path)
    {
        $this->parameters['tempDir'] = $path;
        return $this;
    }

    /**
     * @param bool $debug
     * @return static
     */
    public function setDebugMode(bool $debug)
    {
        $this->parameters['debugMode'] = $debug;
        return $this;
    }

    /**
     * @param array $params
     * @return static
     */
    public function addParameters(array $params)
    {
        $this->parameters = Helpers::merge($params, $this->parameters);
        return $this;
    }

    /**
     * @param array $params
     * @return static
     */
    public function addDynamicParameters(array $params)
    {
        $this->dynamicParameters = $params + $this->dynamicParameters;
        return $this;
    }

    /**
     * @param string|array $config
     * @return static
     */
    public function addConfig($config)
    {
        $this->configs[] = $config;
        return $this;
    }

    public function build(): Container
    {
        if (isset($this->parameters['tempDir'])) {
            $class = $this->loadContainer();
        } else {
            $class = uniqid('Container_');

            $compiler = new Compiler();
            $compiler->setClassName($class);
            $this->configureCompiler($compiler);
            eval($compiler->compile());
        }

        $container = new $class($this->dynamicParameters);
        $container->initialize();
        return $container;
    }

    private function loadContainer()
    {
        $loader = new ContainerLoader(
            $this->parameters['tempDir'] . '/container',
            $this->parameters['debugMode']
        );

        return $loader->load(
            function (Compiler $compiler) { $this->configureCompiler($compiler); },
            [$this->parameters, array_keys($this->dynamicParameters), $this->configs, PHP_VERSION_ID - PHP_RELEASE_VERSION]
        );
    }

    private function configureCompiler(Compiler $compiler)
    {
        $builder = $compiler->getContainerBuilder();
        $builder->addExcludedClasses([
            'ArrayAccess',
            'Countable',
            'IteratorAggregate',
            'stdClass',
            'Traversable',
        ]);

        $loader = new Loader();
        $loader->setParameters($this->parameters);
        foreach ($this->configs as $config) {
            if (is_string($config)) {
                $compiler->loadConfig($config, $loader);
            } else {
                $compiler->addConfig($config);
            }
        }

        $compiler->addConfig(['parameters' => $this->parameters]);
        $compiler->setDynamicParameterNames(array_keys($this->dynamicParameters));

        $compiler->addExtension('slim', new SlimExtension());
        $compiler->addExtension('constants', new \Nette\DI\Extensions\ConstantsExtension());
        $compiler->addExtension('decorator', new \Nette\DI\Extensions\DecoratorExtension());
        $compiler->addExtension('extensions', new \Nette\DI\Extensions\ExtensionsExtension());
        $compiler->addExtension('inject', new \Nette\DI\Extensions\InjectExtension());
        $compiler->addExtension('php', new \Nette\DI\Extensions\PhpExtension());
    }
}
