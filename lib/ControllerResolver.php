<?php
declare(strict_types=1);
namespace Appification;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as Base;
use DI\Container;

class ControllerResolver extends Base
{
    public function __construct(private Container $container)
    {
        parent::__construct($container->get(LoggerInterface::class));
    }

    protected function instantiateController(string $class) : object
    {
        if ($this->container->has($class))
        {
            return $this->container->get($class);
        }
        return $this->container->make($class);
    }
}
