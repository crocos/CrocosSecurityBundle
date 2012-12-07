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
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session');
        $roleManager = new SessionRoleManager($session);
        $roleManager->setDomain('secured');

        $this->session = $session;
        $this->roleManager = $roleManager;
    }

    public function testSetRoles()
    {
        $this->roleManager->setRoles(array('FOO', 'BAR'));

        Phake::verify($this->session)->set('secured/roles', array('FOO', 'BAR'));
    }

    public function testGetRoles()
    {
        Phake::when($this->session)->get('secured/roles', array())->thenReturn(array('FOO', 'BAR'));

        $this->assertEquals(array('FOO', 'BAR'), $this->roleManager->getRoles());
    }
}
