<?php
namespace Crocos\SecurityBundle\Security;

use Symfony\Component\DependencyInjection\Container;

class ContainerParameterResolver implements ParameterResolverInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @var mixed
     */
    public function resolveValue($value)
    {
        return $this->container->getParameterBag()->resolveValue($value);
    }
}
