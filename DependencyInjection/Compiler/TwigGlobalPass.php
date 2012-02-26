<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * TwigGlobalPass.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class TwigGlobalPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Added twig global variable
        if ($container->hasDefinition('twig')) {
            $container->getDefinition('twig')
                ->addMethodCall('addGlobal', array('_security', new Reference('crocos_security.context')))
            ;
        }
    }
}

