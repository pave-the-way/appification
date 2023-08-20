<?php
namespace Appification;

function getApp(string|array $config = null)
{

    $c = buildContainer();

    return $c->get(App::class);
}

use Composer\Autoload\ClassLoader;

use function DI\autowire;
use function DI\value;
use function DI\get;

use DI\ContainerBuilder;
use DI\Container;

use Psr\Log\LoggerInterface;
use Monolog\Logger;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractEventDispatcherInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactoryInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;

use Appification\Router;

function buildContainer() : Container
{
    $builder = new ContainerBuilder;
    $builder->useAutowiring(true);

    $builder->addDefinitions([
        App::class                              => autowire(App::class),
        HttpKernelInterface::class              => get(App::class),

        LoggerInterface::class                  => autowire(
            Logger::class
        )->constructorParameter('name', value('logger')),

        ControllerResolverInterface::class      => autowire(ControllerResolver::class),

        EventDispatcherInterface::class         => autowire(EventDispatcher::class),
        ImmutableEventDispatcher::class         => get(ImmutableEventDispatcher::class),
        ContractEventDispatcherInterface::class => get(EventDispatcherInterface::class),

        RequestStack::class                     => autowire(RequestStack::class),
        RequestContext::class                   => autowire(RequestContext::class),

        ArgumentResolverInterface::class        => autowire(
            ArgumentResolver::class
        )->constructorParameter(
            'argumentMetadataFactory',
            get(ArgumentMetadataFactoryInterface::class)
        )->constructorParameter(
            'argumentValueResolvers',
            get(ArgumentValueResolverSet::class)
        ),

        ArgumentMetadataFactoryInterface::class => autowire(ArgumentMetadataFactory::class),
        ArgumentValueResolverSet::class         => autowire(ArgumentValueResolverSet::class),

        //UrlMatcherInterface::class              => autowire(UrlMatcher::class),
        //RouteCollection::class                  => autowire(RouteCollection::class),
    ]);

    return $builder->build();
}
