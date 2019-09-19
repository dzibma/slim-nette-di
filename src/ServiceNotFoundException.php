<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
}
