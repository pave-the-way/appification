<?php
declare(strict_types=1);
namespace Appification;

use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use IteratorAggregate;
use Traversable;

class ArgumentValueResolverSet implements IteratorAggregate
{
    private array $resolvers = [];

    public function __construct(iterable $resolvers = [], bool $defaults = true)
    {
        if ($defaults)
        {
            foreach(ArgumentResolver::getDefaultArgumentValueResolvers() as $resolver)
            {
                $this->addValueResolver($resolver);
            }
        }

        foreach($resolvers as $resolver)
        {
            $this->addValueResolver($resolver);
        }
    }

    public function getIterator() : Traversable
    {
        foreach($this->resolvers as $resolver)
        {
            yield $resolver;
        }
    }

    public function addValueResolver(ArgumentValueResolverInterface $valueResolver)
    {
        $this->resolvers[] = $valueResolver;
    }

    public function hasValueResolver(string $class) : bool
    {
        foreach($this->resolvers as $resolver)
        {
            if ($resolver instanceof $class)
            {
                return true;
            }
        }
        return false;
    }
}
