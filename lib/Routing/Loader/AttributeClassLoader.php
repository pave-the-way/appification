<?php
declare(strict_types=1);
namespace Appification\Routing\Loader;

use Symfony\Component\Routing\Loader\AnnotationClassLoader;
use Symfony\Component\Routing\Route;

use Doctrine\Common\Annotations\Reader;

use ReflectionMethod;
use ReflectionClass;

class AttributeClassLoader extends AnnotationClassLoader
{
    public function __construct(Reader|bool $annotationSupport = null, string $env = null)
    {
        if (null === $annotationSupport)
        {
            $annotationSupport = class_exists(Reader::class);
        }

        if (is_bool($annotationSupport))
        {
            $annotationSupport = $annotationSupport ? new Reader : null;
        }

        parent::__construct($annotationSupport, $env);
    }

    public function supports(mixed $resource, string $type = null) : bool
    {
        if ($type === 'annotation')
        {
            return $this->supportsAnnotations();
        }
        return parent::supports($resource, $type);
    }

    public function supportsAnnotations() : bool
    {
        return isset($this->reader) && $this->reader instanceof Reader;
    }

    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, object $annot)
    {
        $route->setDefault('_controller', $method->class.'::'.$method->name);
    }
}
