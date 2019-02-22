<?php

namespace Dzibma\Slim\Tests;

use PHPUnit\Framework\TestCase;
use Nette\DI\Compiler;
use Nette\DI\Container;
use Dzibma\Slim\SlimExtension;

class SlimExtensionTest extends TestCase
{
    public function testCreateContainer()
    {
        $compiler = new Compiler();
        $compiler->addExtension('slim', new SlimExtension());
        $class = uniqid('Container');
        $code = $compiler->setClassName($class)->compile();
        eval($code);
        $container = new $class();
        
        $this->assertInstanceOf(Container::class, $container);
        return $container;
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasApplication(Container $container)
    {
        $this->assertNotNull($container->getByType(\Slim\App::class, false));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasSettings(Container $container)
    {
        $this->assertTrue($container->hasService('settings'));
        return $container->getService('settings');
    }

    /**
     * @depends testHasSettings
     */
    public function testSettingsService($settings)
    {
        $this->assertArraySubset([
            'httpVersion' => '1.1',
            'responseChunkSize' => 4096,
            'outputBuffering' => 'append',
            'determineRouteBeforeAppMiddleware' => false,
            'addContentLengthHeader' => true,
            'routerCacheFile' => false,
            'displayErrorDetails' => false
        ], $settings);
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasEnvironment(Container $container)
    {
        $this->assertTrue($container->hasService('environment'));
        return $container->getService('environment');
    }

    /**
     * @depends testHasEnvironment
     */
    public function testEnvironmentService($environment)
    {
        $this->assertInstanceOf(\Slim\Http\Environment::class, $environment);
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasRequest(Container $container)
    {
        $this->assertTrue($container->hasService('request'));
        return $container->getService('request');
    }

    /**
     * @depends testHasRequest
     */
    public function testRequestService($request)
    {
        $this->assertInstanceOf(\Slim\Http\Request::class, $request);
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasResponse(Container $container)
    {
        $this->assertTrue($container->hasService('response'));
        return $container->getService('response');
    }

    /**
     * @depends testHasResponse
     */
    public function testResponseService(\Slim\Http\Response $response)
    {
        $this->assertInstanceOf(\Slim\Http\Response::class, $response);
        $this->assertEquals('text/html; charset=UTF-8', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('1.1', $response->getProtocolVersion());
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasRouter(Container $container)
    {
        $this->assertTrue($container->hasService('router'));
        return $container->getService('router');
    }

    /**
     * @depends testHasRouter
     */
    public function testRouterService($router)
    {
        $this->assertInstanceOf(\Slim\Router::class, $router);
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasFoundHandler(Container $container)
    {
        $this->assertTrue($container->hasService('foundHandler'));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasPhpErrorHandler(Container $container)
    {
        $this->assertTrue($container->hasService('phpErrorHandler'));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasErrorHandler(Container $container)
    {
        $this->assertTrue($container->hasService('errorHandler'));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasNotFoundHandler(Container $container)
    {
        $this->assertTrue($container->hasService('notFoundHandler'));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasNotAllowedHandler(Container $container)
    {
        $this->assertTrue($container->hasService('notAllowedHandler'));
    }

    /**
     * @depends testCreateContainer
     */
    public function testHasCallableResolver(Container $container)
    {
        $this->assertTrue($container->hasService('callableResolver'));
        return $container->getService('callableResolver');
    }

    /**
     * @depends testHasCallableResolver
     */
    public function testCallableResolverService($resolver)
    {
        $this->assertInstanceOf(\Slim\Interfaces\CallableResolverInterface::class, $resolver);
    }
}
