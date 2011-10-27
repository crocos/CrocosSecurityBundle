<?php

namespace Crocos\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Crocos\FrameworkBundle\DependencyInjection\Compiler\StrategyPass;
use Crocos\FrameworkBundle\DependencyInjection\Compiler\FacebookPass;

/**
 * CrocosSecurityBundle.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class CrocosSecurityBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FacebookPass());
        $container->addCompilerPass(new StrategyPass());
    }
}
