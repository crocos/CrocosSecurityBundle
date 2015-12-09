<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SecurityStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // tags:
        //   - { name: crocos_security.auth_logic, alias: myauth }
        $annotationLoaderDefinition = $container->getDefinition('crocos_security.auth_logic_resolver');
        foreach ($container->findTaggedServiceIds('crocos_security.auth_logic') as $id => $attributes) {
            $annotationLoaderDefinition->addMethodCall('registerAuthLogic', [$attributes[0]['alias'], new Reference($id)]);
        }

        // tags:
        //   - { name: crocos_security.http_auth_factory }
        $annotationLoaderDefinition = $container->getDefinition('crocos_security.annotation_loader');
        foreach ($container->findTaggedServiceIds('crocos_security.http_auth_factory') as $id => $attributes) {
            $annotationLoaderDefinition->addMethodCall('addHttpAuthFactory', [new Reference($id)]);
        }

        // tags:
        //   - { name: crocos_security.role_manager, alias: myrolemanager }
        $roleManagerResolverDefinition = $container->getDefinition('crocos_security.role_manager_resolver');
        foreach ($container->findTaggedServiceIds('crocos_security.role_manager') as $id => $attributes) {
            $roleManagerResolverDefinition->addMethodCall('registerRoleManager', [$attributes[0]['alias'], new Reference($id)]);
        }
    }
}
