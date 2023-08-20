<?php
declare(strict_types=1);
namespace Appification;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\AnnotationFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Route;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\FileLocator;

use Doctrine\Common\Annotations\Reader;

class RouteLoader extends DelegatingLoader
{
    public function __construct(FileLocatorInterface $fileLocator = null, array $env = [], Reader $reader = null)
    {
        $fileLocator = $fileLocator ?: new FileLocator;

        $env['attribute'] = isset($env['attribute']) ?: null;
        $env['yaml']  = isset($env['yaml']) ?: null;
        $env['xml']  = isset($env['xml']) ?: null;

        parent::__construct(new LoaderResolver);

        $annotationClassLoader = new AnnotationClassLoader($reader, $env['attribute']);

        $this->addLoader($annotationClassLoader);
        $this->addLoader(new AnnotationDirectoryLoader($fileLocator, $annotationClassLoader));
        $this->addLoader(new AnnotationFileLoader($fileLocator, $annotationClassLoader));

        $this->addLoader(new YamlFileLoader($fileLocator, $env['yaml']));
        $this->addLoader(new XmlFileLoader($fileLocator, $env['xml']));
    }

    public function addLoader(LoaderInterface $loader)
    {
        $this->resolver->addLoader($loader);
    }

    public function getLoaders() : array
    {
        return $this->resolver->getLoaders();
    }
}

class AnnotationClassLoader extends \Symfony\Component\Routing\Loader\AnnotationClassLoader
{
    public function __construct(Reader $reader = null, string $env = null)
    {
        if (null == $reader && class_exists(Reader::class))
        {
            $reader = new Reader;
        }

        parent::__construct($reader, $env);
    }

    protected function configureRoute(Route $route, \ReflectionClass $class, \ReflectionMethod $method, object $annot)
    {
        $route->setDefault('_controller', $method->class.'::'.$method->name);
    }
}
