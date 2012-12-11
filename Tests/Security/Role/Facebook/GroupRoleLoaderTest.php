<?php

namespace Crocos\SecurityBundle\Tests\Security\Role\Facebook;

use Crocos\SecurityBundle\Security\Role\Facebook\GroupRoleLoader;
use Phake;

class GroupRoleLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $facebook;
    protected $loader;

    protected function setUp()
    {
        $this->facebook = Phake::mock('BaseFacebook');
        $this->loader = new GroupRoleLoader();
    }

    public function testLoadRolesReturnsMatchedRoles()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');
        Phake::when($this->facebook)->api('12345/groups')->thenReturn($this->getGroups());

        $roles = $this->loader->loadRoles($this->facebook, array('10000001' => 'FOO', '10000002' => 'BAR'));

        $this->assertEquals(array('FOO', 'BAR'), $roles);
    }

    public function testLoadRolesReturnsEmptyRolesIfUserHasNoMatchedGroups()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');
        Phake::when($this->facebook)->api('12345/groups')->thenReturn($this->getGroups());

        $roles = $this->loader->loadRoles($this->facebook, array('10000003' => 'BAZ'));

        $this->assertEquals(array(), $roles);
    }

    public function testLoadRolesReturnsEmptyRolesIfNotUserLoggedIn()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('0');

        $roles = $this->loader->loadRoles($this->facebook, array('10000001' => 'FOO', '10000002' => 'BAR'));

        $this->assertEquals(array(), $roles);
        Phake::verify($this->facebook, Phake::times(0))->api('12345/groups');
    }

    protected function getGroups()
    {
        return include __DIR__.'/../../../Fixtures/fb_groups.php';
    }
}
