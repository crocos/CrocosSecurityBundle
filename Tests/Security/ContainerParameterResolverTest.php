<?php
namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\ContainerParameterResolver;
use Symfony\Component\DependencyInjection\Container;

class ContainerParameterResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $container = new Container();
        $container->setParameter('foo_param', 'foo');
        $container->setParameter('bar_param', 'bar');

        $this->parameterResolver = new ContainerParameterResolver($container);
    }

    public function testResolveValueResolvesParameterString()
    {
        $result = $this->parameterResolver->resolveValue('%foo_param%%bar_param%');

        $this->assertEquals('foobar', $result);
    }
}
