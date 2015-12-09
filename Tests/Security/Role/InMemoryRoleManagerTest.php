<?php
namespace Crocos\SecurityBundle\Tests\Security\Role;

use Crocos\SecurityBundle\Security\Role\InMemoryRoleManager;
use Phake;

class InMemoryRoleManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $roleManager;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session\SessionInterface');
        $roleManager = new InMemoryRoleManager();
        $roleManager->setDomain('secured');

        $this->roleManager = $roleManager;
    }

    public function testHasRoleReturnsTrueIfEmptyRolesIsPasssed()
    {
        $this->assertTrue($this->roleManager->hasRole([]));
    }

    public function testHasRoleReturnsTrueIfAPassedRoleIsGranted()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);

        $this->assertTrue($this->roleManager->hasRole('FOO'));
    }

    public function testHasRoleReturnsTrueIfPassedRolesContainAnyGrantedRole()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);

        $this->assertTrue($this->roleManager->hasRole(['BAR', 'BAZ']));
    }

    public function testHasRoleReturnsFalseIfPassedRolesDoesNotContaineGrantedRole()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);

        $this->assertFalse($this->roleManager->hasRole('XYZ'));
    }

    public function testSetAndGetRoles()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);

        $this->assertSame(['FOO', 'BAR'], $this->roleManager->getRoles());
    }

    public function testAddRoles()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);
        $this->roleManager->addRoles('BAZ');

        $this->assertSame(['FOO', 'BAR', 'BAZ'], $this->roleManager->getRoles());
    }

    public function testClearRoles()
    {
        $this->roleManager->setRoles(['FOO', 'BAR']);
        $this->roleManager->clearRoles();

        $this->assertSame([], $this->roleManager->getRoles());
    }

    public function testPreload()
    {
        $this->assertFalse($this->roleManager->isPreloaded());

        $this->roleManager->setPreloaded();

        $this->assertTrue($this->roleManager->isPreloaded());
    }
}
