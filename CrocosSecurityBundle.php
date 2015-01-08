<?php

namespace Crocos\SecurityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Crocos\SecurityBundle\DependencyInjection\Compiler;

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

        $container->addCompilerPass(new Compiler\DoctrineAuthPass());
        $container->addCompilerPass(new Compiler\TwigGlobalPass());

        $container->addCompilerPass(new Compiler\AuthLogicPass());
        $container->addCompilerPass(new Compiler\HttpAuthFactoryPass());
        $container->addCompilerPass(new Compiler\RoleManagerPass());
    }
}
