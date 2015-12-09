<?php
namespace Crocos\SecurityBundle;

use Crocos\SecurityBundle\DependencyInjection\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
