<?php

namespace Crocos\SecurityBundle\Tests\Security\Role;

use Crocos\SecurityBundle\Security\Role\SessionRoleManager;
use Phake;

class SessionRoleManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $roleManager;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');
        $roleManager = new SessionRoleManager($session);
        $roleManager->setDomain('secured');

        $this->session = $session;
        $this->roleManager = $roleManager;
    }

    public function testHasRoleReturnsTrueIfEmptyRolesIsPasssed()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array());

        $this->assertTrue($this->roleManager->hasRole(array()));
    }

    public function testHasRoleReturnsTrueIfAPassedRoleIsGranted()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->assertTrue($this->roleManager->hasRole('FOO'));
    }

    public function testHasRoleReturnsTrueIfPassedRolesContainAnyGrantedRole()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->assertTrue($this->roleManager->hasRole(array('BAR', 'BAZ')));
    }

    public function testHasRoleReturnsFalseIfPassedRolesDoesNotContaineGrantedRole()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->assertFalse($this->roleManager->hasRole('XYZ'));
    }

    public function testSetRoles()
    {
        $this->roleManager->setRoles(array('FOO', 'BAR'));

        Phake::verify($this->session)->set('secured/role/roles', array('FOO', 'BAR'));
    }

    public function testAddRoles()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->roleManager->addRoles('BAZ');

        Phake::verify($this->session)->set('secured/role/roles', array('FOO', 'BAR', 'BAZ'));
    }

    public function testGetRoles()
    {
        Phake::when($this->session)->get('secured/role/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->assertEquals(array('FOO', 'BAR'), $this->roleManager->getRoles());
    }
}
