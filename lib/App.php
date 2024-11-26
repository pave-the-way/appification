<?php
declare(strict_types=1);
namespace Appification;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\EventListener\ResponseListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\EventListener\DisallowRobotsIndexingListener;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;

use DI\Container;

class App implements HttpKernelInterface, TerminableInterface
{
    private HttpKernel $wrapped;

    public function __construct(private Container $container)
    {}

    final public function run(Request $request = null) : void
    {
        $request = $request ?: Request::createFromGlobals();
        $response = $this->handle($request);
        $response->send();
        $this->terminate($request, $response);
    }

    final public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true) : Response
    {
        $this->boot();
        return $this->wrapped->handle($request, $type, $catch);
    }

    final public function terminate(Request $request, Response $response) : void
    {
        $this->boot();
        $this->wrapped->terminate($request, $response);
    }

    final public function boot() : void
    {
        if (isset($this->wrapped))
        {
            return;
        }
        $eventDispatcher = $this->container->get(EventDispatcherInterface::class);

        $router = $this->container->get(Routing\Router::class);

        foreach ($this->getRouteResources() as $res)
        {
            $router->loadRoutes($res);
        }
        //$router->compile();
        //$router->unOptimize();
        $this->wrapped = new HttpKernel(
            $eventDispatcher,
            $this->container->get(ControllerResolverInterface::class),
            $this->container->get(RequestStack::class),
            $this->container->get(ArgumentResolverInterface::class)
        );

        $subscribers = [
            new RouterListener(
                $router,
                $this->container->get(RequestStack::class),
                $this->container->get(RequestContext::class),
                $this->container->get(LoggerInterface::class)
            ),
            new ResponseListener('UTF-8'),
            new DisallowRobotsIndexingListener
        ];

        foreach ($subscribers as $subscriber)
        {
            $eventDispatcher->addSubscriber($subscriber);
        }
    }

    final public function getContainer() : Container
    {
        return $this->container;
    }

    protected function getRouteResources() : array
    {
        return [
            __DIR__ . '/Controller'
        ];
    }
}
