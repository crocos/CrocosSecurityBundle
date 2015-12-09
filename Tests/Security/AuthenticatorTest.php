<?php
namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\Authenticator;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;
use Symfony\Component\HttpFoundation\Request;

class AuthenticatorTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $matcher;
    protected $authenticator;
    protected $controller;

    protected function setUp()
    {
        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($context)->getForwardingController()->thenReturn('Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction');

        $loader = Phake::mock('Crocos\SecurityBundle\Security\AnnotationLoader');
        $matcher = Phake::mock('Crocos\SecurityBundle\Security\ForwardingControllerMatcher');

        $authenticator = new Authenticator($loader, $matcher);

        $this->context = $context;
        $this->matcher = $matcher;
        $this->authenticator = $authenticator;

        $this->controller = [new Fixtures\UserController(), 'securedAction'];
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfControllerIsNotSecure()
    {
        Phake::when($this->context)->isSecure()->thenReturn(false);

        $this->authenticator->authenticate($this->context, $this->controller);
    }

    /**
     * @expectedException Crocos\SecurityBundle\Exception\AuthException
     */
    public function testAuthenticateThrowsAuthExceptionIfUserIsNotAuthenticated()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);
        Phake::when($this->context)->isAuthenticated()->thenReturn(false);

        $this->authenticator->authenticate($this->context, $this->controller);
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfUserIsAlreadyAuthenticated()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);
        Phake::when($this->context)->isAuthenticated()->thenReturn(true);

        $this->authenticator->authenticate($this->context, $this->controller);
    }

    public function testAuthenticateDoesNotThrowAuthExceptionIfControllerMatchesForwardingController()
    {
        Phake::when($this->context)->isSecure()->thenReturn(true);

        $reflObject = new \ReflectionObject($this->controller[0]);
        $reflMethod = $reflObject->getMethod($this->controller[1]);
        Phake::when($this->matcher)->isForwardingController($this->context, $reflObject, $reflMethod)->thenReturn(true);

        $this->authenticator->authenticate($this->context, $this->controller);
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
        Phake::when($this->context)->getHttpAuths()->thenReturn([
            'test' => $httpAuth,
        ]);

        $this->authenticator->authenticate($this->context, $this->controller, $request);
    }
}
