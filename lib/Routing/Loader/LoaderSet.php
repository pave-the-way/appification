<?php
declare(strict_types=1);
namespace Appification\Routing\Loader;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\FileLocator;

use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\PhpFileLoader;

use Doctrine\Common\Annotations\Reader;

class LoaderSet extends DelegatingLoader
{
    public function __construct(array $loaders = [])
    {
        parent::__construct(new LoaderResolver($loaders));
    }

    public static function create(FileLocatorInterface|string|array $locator = null, bool|Reader $annotationSupport = null, array|string $env = null) : static
    {
        if (false == ($locator instanceof FileLocatorInterface))
        {
            $locator = new FileLocator((array) $locator);
        }

        if (is_array($env))
        {
            $env['attribute'] = isset($env['attribute']) ? $env['attribute'] : null;
            $env['yaml']  = isset($env['yaml']) ? $env['yaml'] : null;
            $env['xml']  = isset($env['xml']) ? $env['xml'] : null;
            $env['php']  = isset($env['php']) ? $env['php'] : null;
        }
        else
        {
            $env = [
                'attribute' => $env,
                'yaml'      => $env,
                'xml'       => $env,
                'php'       => $env
            ];
        }

        return new static([
            $attributeLoader = new AttributeClassLoader($annotationSupport, $env['attribute']),
            new AnnotationDirectoryLoader($locator, $attributeLoader),
            new YamlFileLoader($locator, $env['yaml']),
            new XmlFileLoader($locator, $env['xml']),
            new PhpFileLoader($locator, $env['php'])
        ]);
    }

    public function addLoader(LoaderInterface $loader) : void
    {
        $this->resolver->addLoader($loader);
    }

    public function getLoaders() : array
    {
        return $this->resolver->getLoaders();
    }
}
