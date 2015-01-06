<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * RoleManagerPass.
 *
 * @author Toshiyuki Fujita <tofujiit@crocos.co.jp>
 */
class RoleManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('crocos_security.role_manager_resolver');

        // tags:
        //   - { name: crocos_security.role_manager, alias: myrolemanager }
        foreach ($container->findTaggedServiceIds('crocos_security.role_manager') as $id => $attributes) {
            $definition->addMethodCall('registerRoleManager', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
