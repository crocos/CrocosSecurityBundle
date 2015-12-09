<?php

namespace Crocos\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Crocos\SecurityBundle\DependencyInjection\Compiler;

class CrocosSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new Compiler\SecurityStrategyPass());

        // bridges
        $container->addCompilerPass(new Compiler\DoctrineAuthPass());
        $container->addCompilerPass(new Compiler\TwigGlobalPass());
    }
}
