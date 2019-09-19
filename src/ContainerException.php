<?php

declare(strict_types=1);

namespace Dzibma\SlimNette;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \RuntimeException implements ContainerExceptionInterface
{
}
