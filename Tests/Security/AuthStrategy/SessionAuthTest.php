<?php

namespace Crocos\SecurityBundle\Tests\Security\AuthStrategy;

use Crocos\SecurityBundle\Security\AuthStrategy\SessionAuth;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SessionAuthTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $auth;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session');
        $auth = new SessionAuth($session);
        $auth->setDomain('default');

        $this->session = $session;
        $this->auth = $auth;
    }

    public function testLogin()
    {
        $this->auth->login('crocos');

        Phake::verify($this->session)->migrate();
        Phake::verify($this->session)->set('default._authenticated', true);
        Phake::verify($this->session)->set('default._user', 'crocos');
    }

    public function testLogout()
    {
        $this->auth->logout();

        Phake::verify($this->session)->invalidate();
        Phake::verify($this->session)->set('default._authenticated', false);
        Phake::verify($this->session)->set('default._user', null);
    }

    public function testAuthenticated()
    {
        Phake::when($this->session)->get('default._authenticated', false)->thenReturn(true);

        $this->assertTrue($this->auth->isAuthenticated());
    }

    public function testNotAuthenticated()
    {
        Phake::when($this->session)->get('default._authenticated', false)->thenReturn(false);

        $this->assertFalse($this->auth->isAuthenticated());
    }

    public function testGetUser()
    {
        Phake::when($this->session)->get('default._user', null)->thenReturn('crocos');

        $this->assertEquals('crocos', $this->auth->getUser());
    }

    public function testGetUserCoughtException()
    {
        Phake::when($this->session)->get('default._user', null)->thenThrow(new \Exception());

        try {
            $this->auth->getUser();
        } catch (\Exception $e) {
            Phake::verify($this->session)->set('default._authenticated', false);
            Phake::verify($this->session)->set('default._user', null);

            return;
        }

        $this->fail();
    }
}
