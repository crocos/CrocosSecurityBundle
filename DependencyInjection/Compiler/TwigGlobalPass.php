<?php
namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TwigGlobalPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig')) {
            return;
        }

        // Added twig global variable
        $container->getDefinition('twig')
            ->addMethodCall('addGlobal', ['security', new Reference('crocos_security.context')])
        ;
    }
}
