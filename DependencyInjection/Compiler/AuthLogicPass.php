<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * AuthLogicPass.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthLogicPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('crocos_security.auth_logic_resolver');

        // tags:
        //   - { name: crocos_security.auth_logic, alias: myauth }
        foreach ($container->findTaggedServiceIds('crocos_security.auth_logic') as $id => $attributes) {
            $definition->addMethodCall('registerAuthLogic', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
