<?php

namespace Crocos\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * CrocosSecurityExtension configuration structure.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class Configuration implements ConfigurationInterface
{
    private $debug;

    public function __construct($debug)
    {
        $this->debug = (Boolean) $debug;
    }

    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('crocos_security');

        $rootNode
            ->children()
                ->booleanNode('https_requiring')
                    ->defaultTrue()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
