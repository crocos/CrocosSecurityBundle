<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * StrategyPass.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class StrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('crocos_security.auth_strategy_resolver');

        foreach ($container->findTaggedServiceIds('crocos_security.strategy') as $id => $attributes) {
            $definition->addMethodCall('addStrategy', array($attributes[0]['alias'], new Reference($id)));
        }
    }
}
