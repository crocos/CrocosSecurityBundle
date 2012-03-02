<?php

namespace Crocos\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * DoctrineAuthPass.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class DoctrineAuthPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine')) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../Resources/config'));
        $loader->load('doctrine.yml');
    }
}