<?php

namespace Crocos\SecurityBundle\Tests\Security\AuthLogic;

use Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver;
use Phake;

class AuthLogicResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $resolver;

    protected function setUp()
    {
        $resolver = new AuthLogicResolver();

        $this->resolver = $resolver;
    }

    public function testResolveAuthLogic()
    {
        $logic = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface');
        $this->resolver->registerAuthLogic('foo', $logic);

        $this->assertEquals($logic, $this->resolver->resolveAuthLogic('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testResolveAuthLogicThrowInvalidArgumentExceptionIfNotRegistered()
    {
        $this->resolver->resolveAuthLogic('wozozo');
    }
}
