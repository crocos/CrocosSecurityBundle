<?php

namespace Crocos\SecurityBundle\Tests\Security\AuthLogic;

use Crocos\SecurityBundle\Security\AuthLogic\FacebookAuth;
use Phake;

class FacebookAuthTest extends \PHPUnit_Framework_TestCase
{
    protected $facebook;
    protected $auth;

    protected function setUp()
    {
        if (!class_exists('BaseFacebook')) {
            $this->markTestSkipped('Facebook is not available.');
        }

        $facebook = Phake::mock('\BaseFacebook');
        $auth = new FacebookAuth($facebook);

        $this->facebook = $facebook;
        $this->auth = $auth;
    }

    public function testAuthenticated()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');

        $this->assertTrue($this->auth->isAuthenticated());
    }

    public function testNotAuthenticated()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('0');

        $this->assertFalse($this->auth->isAuthenticated());
    }

    public function testGetUserRetrieveFacebookUser()
    {
        Phake::when($this->facebook)->getUser()->thenReturn('12345');

        $this->assertEquals('12345', $this->auth->getUser());
    }
}
