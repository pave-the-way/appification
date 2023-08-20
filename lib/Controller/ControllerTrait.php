<?php
declare(strict_types=1);
namespace Appification\Controller;

use DI\Container;

trait ControllerTrait
{
    public function __construct(private Container $container)
    {}

    protected function getContainer()
    {
        return $this->container;
    }
}
