<?php
declare(strict_types=1);
namespace Appification\Routing;

use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

interface RouterInterface extends UrlMatcherInterface, RequestMatcherInterface
{
    public function getLoader() : LoaderInterface;

    public function loadRoutes(mixed $res, string $type = null) : void;

    public function addRoute(Route $route, string $name = null, int $priority = 0) : void;

    public function addRoutes(RouteCollection $collection) : void;

    public function getRoutes() : RouteCollection;
}
