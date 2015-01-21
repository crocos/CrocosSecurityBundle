<?php

namespace Crocos\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * CrocosSecurityExtension.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class CrocosSecurityExtension extends Extension
{
    /**
     * Load the configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @see Compiler\FacebookPass
     * @see Compiler\AuthLogicPass
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('services.yml');

        $container->setParameter('crocos_security.https_requiring', $config['https_requiring']);
    }
}
