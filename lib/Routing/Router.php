<?php
declare(strict_types=1);
namespace Appification\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;

use Symfony\Component\HttpFoundation\Request;

class Router implements RouterInterface
{
    private UrlMatcher|CompiledUrlMatcher $matcher;

    private ?LoaderInterface $loader;

    public function __construct(RequestContext $context, LoaderInterface $loader = null)
    {
        $this->matcher = new UrlMatcher(new RouteCollection, $context);
        $this->loader = $loader;
    }

    public function match(string $pathinfo) : array
    {
        return $this->matcher->match($pathinfo);
    }

    public function matchRequest(Request $request) : array
    {
        return $this->matcher->matchRequest($request);
    }

    public function setContext(RequestContext $context) : void
    {
        $this->matcher->setContext($context);
    }

    public function getContext() : RequestContext
    {
        return $this->matcher->getContext();
    }

    public function getLoader() : LoaderInterface
    {
        return $this->loader ? $this->loader : static::defaultLoader();
    }

    public function loadRoutes(mixed $res, string $type = null) : void
    {
        if ($routes = $this->getLoader()->load($res, $type))
        {
            $this->addRoutes($routes);
        }
    }

    public function addRoute(Route $route, string $name = null, int $priority = 0) : void
    {
        $name = $name ?: bin2hex(random_bytes(12));
        $this->getRoutes()->add($name, $route, $priority);
    }

    public function addRoutes(RouteCollection $collection) : void
    {
        $this->getRoutes()->addCollection($collection);
    }

    public function getRoutes() : RouteCollection
    {
        static $cb;

        if ($this->matcher instanceof CompiledUrlMatcher)
        {

        }

        if (null == $cb)
        {
            $cb = function()
            {
                return $this->routes;
            };
        }

        return $cb->call($this->matcher);
    }

    public function compile() : void
    {
        if (false == ($this->matcher instanceof CompiledUrlMatcher))
        {
            $compiler = new CompiledUrlMatcherDumper($this->getRoutes());
            $this->matcher = new CompiledUrlMatcher($compiler->getCompiledRoutes(), $this->matcher->getContext());
        }
    }

    protected static function defaultLoader() : LoaderInterface
    {
        static $loader;

        if (null == $loader)
        {
            $loader = Loader\LoaderSet::create();
        }

        return $loader;
    }
}
