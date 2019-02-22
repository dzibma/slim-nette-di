<?php

namespace Dzibma\Slim;

use Psr\Container\ContainerInterface;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;

class ContainerAdapter implements ContainerInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
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
                : $this->container->getService($id);

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
            : $this->container->hasService($id);
    }
}
