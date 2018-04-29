<?php

namespace Dzibma\Slim;

use Psr\Container\ContainerInterface;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;

class ContainerAdapter implements ContainerInterface
{
    private static $slimServices = [
        'settings' => true,
        'environment' => true,
        'request' => true,
        'response' => true,
        'router' => true,
        'foundHandler' => true,
        'phpErrorHandler' => true,
        'errorHandler' => true,
        'notFoundHandler' => true,
        'notAllowedHandler' => true,
        'callableResolver' => true
    ];

    /** @var string */
    private $slimPrefix;

    /** @var Container */
    private $container;

    public function __construct($slimPrefix, Container $container)
    {
        $this->slimPrefix = $slimPrefix;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        try {
            return class_exists($id) || interface_exists($id)
                ? $this->container->getByType($id)
                : $this->container->getService($this->prefixSlimService($id));

        } catch (MissingServiceException $e) {
            throw new ServiceNotFoundException($e->getMessage(), $e->getCode(), $e);
        } catch (\Exception $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return class_exists($id) || interface_exists($id)
            ? $this->container->getByType($id, false) !== null
            : $this->container->hasService($this->prefixSlimService($id));
    }

    private function prefixSlimService($id)
    {
        if (isset(self::$slimServices[$id])) {
            $id = "{$this->slimPrefix}.$id";
        }

        return $id;
    }
}
