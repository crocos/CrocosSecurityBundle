<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ContainerParameterResolver
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class ContainerParameterResolver implements ParameterResolverInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolveValue($value)
    {
        return $this->container->getParameterBag()->resolveValue($value);
    }
}
