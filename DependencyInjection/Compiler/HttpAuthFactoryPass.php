<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * HttpAuthFactoryPass.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class HttpAuthFactoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('crocos_security.annotation_loader');

        // tags:
        //   - { name: crocos_security.http_auth_factory }
        foreach ($container->findTaggedServiceIds('crocos_security.http_auth_factory') as $id => $attributes) {
            $definition->addMethodCall('addHttpAuthFactory', [new Reference($id)]);
        }
    }
}
