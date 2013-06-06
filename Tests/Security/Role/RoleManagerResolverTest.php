<?php

namespace Crocos\SecurityBundle\Tests\Security\Role;

use Crocos\SecurityBundle\Security\Role\RoleManagerResolver;
use Phake;

class RoleManagerResolverTest extends \PHPUnit_Framework_TestCase
{
    protected $resolver;

    protected function setUp()
    {
        $resolver = new RoleManagerResolver();

        $this->resolver = $resolver;
    }

    public function testResolveRoleManager()
    {
        $roleManager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        $this->resolver->registerRoleManager('foo', $roleManager);

        $this->assertEquals($roleManager, $this->resolver->resolveRoleManager('foo'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testResolveRoleManagerThrowInvalidArgumentExceptionIfNotRegistered()
    {
        $this->resolver->resolveRoleManager('wozozo');
    }
}
