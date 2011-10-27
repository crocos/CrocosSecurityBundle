<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\SecurityChecker;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SecurityCheckerTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $matcher;
    protected $checker;

    protected $object;
    protected $method;

    protected function setUp()
    {
        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($context)->getForwardingController()->thenReturn('Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction');

        $loader = Phake::mock('Crocos\SecurityBundle\Security\AnnotationLoader');
        $matcher = Phake::mock('Crocos\SecurityBundle\Security\ForwardingControllerMatcher');

        $checker = new SecurityChecker($context, $loader, $matcher);

        $this->context = $context;
        $this->matcher = $matcher;
        $this->checker = $checker;

        $this->object = new Fixtures\UserController();
        $this->method = 'securedAction';
    }

    public function testSecureControllerForwardLoginController()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);

        $this->assertEquals($this->context->getForwardingController(), $this->checker->checkSecurity($this->object, $this->method));
    }

    public function testNotSecureControllerNotForward()
    {
        Phake::when($this->context)->isSecure()->thenReturn(false);

        $this->assertEmpty($this->checker->checkSecurity($this->object, $this->method));
    }

    public function testSecureControllerNotForwardIfLoggedIn()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);
        Phake::when($this->context)->isAuthenticated()->thenReturn(true);

        $this->assertEmpty($this->checker->checkSecurity($this->object, $this->method));
    }

    public function testIsSecureControllerThatAnnotatedSecureAnnotation()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);

        $reflObject = new \ReflectionObject($this->object);
        $reflMethod = $reflObject->getMethod($this->method);
        Phake::when($this->matcher)->isForwardingController($this->context, $reflObject, $reflMethod)->thenReturn(true);

        $this->assertEmpty($this->checker->checkSecurity($this->object, $this->method));
    }
}
