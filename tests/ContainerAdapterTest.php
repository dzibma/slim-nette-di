<?php

namespace Dzibma\Slim\Tests;

use PHPUnit\Framework\TestCase;
use Nette\DI\Container;
use Nette\DI\Compiler;
use Psr\Container\ContainerInterface;
use Dzibma\Slim\ContainerAdapter;
use Dzibma\Slim\Tests\Dummy\Service;
use Dzibma\Slim\Tests\Dummy\ServiceInterface;

class ContainerAdapterTest extends TestCase
{
    private static $slimServices = [
        'settings',
        'environment',
        'request',
        'response',
        'router',
        'foundHandler',
        'phpErrorHandler',
        'errorHandler',
        'notFoundHandler',
        'notAllowedHandler',
        'callableResolver'
    ];

    public function slimPrefixedServiceProvider()
    {
        foreach (self::$slimServices as $service) {
            yield [$service];
        }
    }

    public function testContainerAdapter()
    {
        $compiler = new Compiler();
        $compiler->addConfig([
            'services' => [
                'service' => Service::class
            ]
        ]);
        $class = uniqid('Container');
        $code = $compiler->setClassName($class)->compile();
        eval($code);
        $container = new $class();

        foreach (self::$slimServices as $id) {
            $container->addService("slim.$id", new \stdClass());
        }

        $containerAdapter = new ContainerAdapter('slim', $container);
        $this->assertInstanceOf(ContainerInterface::class, $containerAdapter);
        return $containerAdapter;
    }

    /**
     * @depends testContainerAdapter
     * @dataProvider slimPrefixedServiceProvider
     */
    public function testPrefixedSlimServices($id, ContainerInterface $container)
    {
        $this->assertTrue($container->has($id));
    }

    /**
     * @depends testContainerAdapter
     */
    public function testGetServiceByName(ContainerInterface $container)
    {
        $this->assertInstanceOf(Service::class, $container->get('service'));
    }

    /**
     * @depends testContainerAdapter
     */
    public function testGetServiceByClass(ContainerInterface $container)
    {
        $this->assertInstanceOf(Service::class, $container->get(Service::class));
    }

    /**
     * @depends testContainerAdapter
     */
    public function testGetServiceByInterface(ContainerInterface $container)
    {
        $this->assertInstanceOf(ServiceInterface::class, $container->get(Service::class));
    }

    /**
     * @depends testContainerAdapter
     * @expectedException \Psr\Container\NotFoundExceptionInterface
     */
    public function testGetUndefinedService(ContainerInterface $container)
    {
        $container->get('missingService');
    }

    /**
     * @depends testContainerAdapter
     */
    public function testHasServiceByName(ContainerInterface $container)
    {
        $this->assertTrue($container->has('service'));
        $this->assertFalse($container->has('missingService'));
    }

    /**
     * @depends testContainerAdapter
     */
    public function testHasServiceByClass(ContainerInterface $container)
    {
        $this->assertTrue($container->has(Service::class));
    }

    /**
     * @depends testContainerAdapter
     */
    public function testHasServiceByInterface(ContainerInterface $container)
    {
        $this->assertTrue($container->has(ServiceInterface::class));
    }
}