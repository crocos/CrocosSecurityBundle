<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $auth;

    protected function setUp()
    {
        $context = new SecurityContext();

        $auth = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface');
        $context->setAuthLogic($auth);

        $this->context = $context;
        $this->auth = $auth;
    }

    public function testContextBeforeLoading()
    {
        $context = new SecurityContext();

        $this->assertEmpty($context->getUser());
        $this->assertFalse($context->isAuthenticated());
        $this->assertEquals('secured', $context->getDomain());
    }

    public function testDelegateToLogic()
    {
        $context = $this->context;

        $context->login('user');
        $context->logout();
        $context->getUser();
        $context->isAuthenticated();

        Phake::verify($this->auth)->login('user');
        Phake::verify($this->auth)->logout();
        Phake::verify($this->auth)->getUser();
        Phake::verify($this->auth)->isAuthenticated();
    }

    public function testForwardingController()
    {
        $this->context->setForwardingController('SecurityController::loginAction');

        $this->assertEquals('SecurityController::loginAction', $this->context->getForwardingController());
    }

    public function testPreviousUrlHolder()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $this->context->setPreviousUrlHolder($holder);

        $this->assertEquals($holder, $this->context->getPreviousUrlHolder());
    }

    public function testHasPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        Phake::when($holder)->has()->thenReturn(true);

        $this->context->setPreviousUrlHolder($holder);

        $this->assertTrue($this->context->hasPreviousUrl());
    }

    public function testSetPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');

        $this->context->setPreviousUrlHolder($holder);

        $this->context->setPreviousUrl('http://example.com/previous');

        Phake::verify($holder)->set('http://example.com/previous');
    }

    public function testGetPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        Phake::when($holder)->get()->thenReturn('http://example.com/previous');

        $this->context->setPreviousUrlHolder($holder);

        $this->assertEquals('http://example.com/previous', $this->context->getPreviousUrl());
    }

    public function testUnuseHttpAuthByDefault()
    {
        $this->assertFalse($this->context->useHttpAuth());
    }

    public function testUseHttpAuth()
    {
        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $this->context->setHttpAuth($httpAuth);

        $this->assertTrue($this->context->useHttpAuth());
    }
}
