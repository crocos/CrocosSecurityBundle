<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Request;
use Crocos\SecurityBundle\Security\AuthChecker;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AuthCheckerTest extends \PHPUnit_Framework_TestCase
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

        $checker = new AuthChecker($loader, $matcher);

        $this->context = $context;
        $this->matcher = $matcher;
        $this->checker = $checker;

        $this->object = new Fixtures\UserController();
        $this->method = 'securedAction';
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfControllerIsNotSecure()
    {
        Phake::when($this->context)->isSecure()->thenReturn(false);

        $this->checker->authenticate($this->context, $this->object, $this->method);
    }

    /**
     * @expectedException Crocos\SecurityBundle\Exception\AuthException
     */
    public function testAuthenticateThrowsAuthExceptionIfUserIsNotAuthenticated()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);
        Phake::when($this->context)->isAuthenticated()->thenReturn(false);

        $this->checker->authenticate($this->context, $this->object, $this->method);
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfUserIsAlreadyAuthenticated()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);
        Phake::when($this->context)->isAuthenticated()->thenReturn(true);

        $this->checker->authenticate($this->context, $this->object, $this->method);
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfControllerMatchesForwardingController()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);

        $reflObject = new \ReflectionObject($this->object);
        $reflMethod = $reflObject->getMethod($this->method);
        Phake::when($this->matcher)->isForwardingController($this->context, $reflObject, $reflMethod)->thenReturn(true);

        $this->checker->authenticate($this->context, $this->object, $this->method);
    }

    /**
     * @expectedException Crocos\SecurityBundle\Exception\HttpAuthException
     */
    public function testAuthenticateThrowsHttpAuthExceptionIfHttpAuthenticationFailed()
    {
        $request = Request::create('/');

        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        Phake::when($httpAuth)->authenticate($request)->thenReturn(false);

        Phake::when($this->context)->useHttpAuth()->thenReturn(true);
        Phake::when($this->context)->getHttpAuth()->thenReturn($httpAuth);

        $this->checker->authenticate($this->context, $this->object, $this->method, $request);
    }

    public function testAuthorizeDoesNotThrowAuthExceptionIfHasAllowedRoles()
    {
        Phake::when($this->context)->getAllowedRoles()->thenReturn(array('FOO'));
        Phake::when($this->context)->hasRole(array('FOO'))->thenReturn(true);

        $this->checker->authorize($this->context);
    }

    /**
     * @expectedException Crocos\SecurityBundle\Exception\AuthException
     */
    public function testAuthorizeThrowsAuthExceptionIfHasNotAllowedRoles()
    {
        Phake::when($this->context)->getAllowedRoles()->thenReturn(array('FOO'));
        Phake::when($this->context)->hasRole(array('FOO'))->thenReturn(false);

        $this->checker->authorize($this->context);
    }
}
