<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

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
            return $this->container->getByType($id, false) ?? $this->container->getService($id);
        } catch (MissingServiceException $e) {
            throw new ServiceNotFoundException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        try {
            return $this->container->hasService($id) || $this->container->getByType($id, false) !== null;
        } catch (\Throwable $e) {
            throw new ContainerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
