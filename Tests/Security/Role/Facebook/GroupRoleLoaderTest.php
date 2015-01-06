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
        if (!class_exists('BaseFacebook')) {
            $this->markTestSkipped('Facebook is not available.');
        }

        $this->facebook = Phake::mock('BaseFacebook');
        $this->loader = new GroupRoleLoader();
    }

    public function testLoadRolesReturnsMatchedRoles()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');
        Phake::when($this->facebook)->api('12345/groups')->thenReturn($this->getGroups());

        $roles = $this->loader->loadRoles($this->facebook, ['10000001' => 'FOO', '10000002' => 'BAR']);

        $this->assertEquals(['FOO', 'BAR'], $roles);
    }

    public function testLoadRolesReturnsEmptyRolesIfUserHasNoMatchedGroups()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');
        Phake::when($this->facebook)->api('12345/groups')->thenReturn($this->getGroups());

        $roles = $this->loader->loadRoles($this->facebook, ['10000003' => 'BAZ']);

        $this->assertEquals([], $roles);
    }

    public function testLoadRolesReturnsEmptyRolesIfNotUserLoggedIn()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('0');

        $roles = $this->loader->loadRoles($this->facebook, ['10000001' => 'FOO', '10000002' => 'BAR']);

        $this->assertEquals([], $roles);
        Phake::verify($this->facebook, Phake::times(0))->api('12345/groups');
    }

    protected function getGroups()
    {
        return include __DIR__ . '/../../../Fixtures/fb_groups.php';
    }
}
