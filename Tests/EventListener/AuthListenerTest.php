<?php

namespace Crocos\SecurityBundle\Tests\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;
use Crocos\SecurityBundle\EventListener\AuthListener;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AuthListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $authenticator;
    protected $authorizer;
    protected $context;
    protected $request;
    protected $resolver;
    protected $kernel;
    protected $event;
    protected $controller;

    protected function setUp()
    {
        $authenticator = Phake::mock('Crocos\SecurityBundle\Security\AuthenticatorInterface');
        $authorizer = Phake::mock('Crocos\SecurityBundle\Security\AuthorizerInterface');

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');

        $query = ['a' => 'b'];
        $request = Request::create('/', 'GET', $query);

        $resolver = Phake::mock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');

        $kernel = Phake::mock('Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel');

        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->context = $context;
        $this->query = $query;
        $this->request = $request;
        $this->resolver = $resolver;
        $this->kernel = $kernel;
    }

    public function testHandleRequest()
    {
        $controller = [new Fixtures\AdminController(), 'securedAction'];
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $this->fixKernelEventMock($event);

        $listener = new AuthListener($this->context, $this->authenticator, $this->authorizer, $this->resolver);
        $listener->onKernelRequest($event);

        Phake::verify($this->authenticator)->authenticate($this->context, $controller, $this->request);
        Phake::verify($this->authorizer)->authorize($this->context);
    }

    public function testHandleAuthException()
    {
        $controller = [Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\AdminController'), 'securedAction'];
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        $attrs = ['foo' => 'bar'];
        $exception = new AuthException('error', $attrs);
        Phake::when($event)->getException()->thenReturn($exception);

        $forwardingController = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        Phake::when($this->context)->getForwardingController()->thenReturn($forwardingController);

        $response = new Response('test');
        Phake::when($controller[0])->forward($forwardingController, $attrs, $this->query)->thenReturn($response);

        $listener = new AuthListener($this->context, $this->authenticator, $this->authorizer, $this->resolver);
        $listener->onKernelException($event);

        Phake::verify($this->context)->setPreviousUrl($this->request->getUri());
        Phake::verify($event)->setResponse($response);
        $this->assertEquals(200, $response->headers->get('X-Status-Code'));
    }

    public function testHandleHttpAuthException()
    {
        $controller = [new Fixtures\BasicSecurityController(), 'securedAction'];
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        $exception = new HttpAuthException('basic', 'error');
        Phake::when($event)->getException()->thenReturn($exception);

        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $response = new Response('Authentication required', 401);
        Phake::when($httpAuth)->createUnauthorizedResponse($this->request, $exception)->thenReturn($response);
        Phake::when($this->context)->useHttpAuth()->thenReturn(true);
        Phake::when($this->context)->getHttpAuth('basic')->thenReturn($httpAuth);

        $listener = new AuthListener($this->context, $this->authenticator, $this->authorizer, $this->resolver);
        $listener->onKernelException($event);

        Phake::verify($httpAuth)->createUnauthorizedResponse($this->request, $exception);
        Phake::verify($event)->setResponse($response);
        $this->assertEquals(401, $response->headers->get('X-Status-Code'));
    }

    public function testHandleHttpsRequiredAuthException()
    {
        $controller = [Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\AdminController'), 'securedAction'];
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        $exception = new HttpsRequiredException();
        Phake::when($event)->getException()->thenReturn($exception);

        $listener = new AuthListener($this->context, $this->authenticator, $this->authorizer, $this->resolver);
        $listener->onKernelException($event);

        Phake::verify($event)->setResponse(Phake::capture($redirectResponse));

        $this->assertStringStartsWith('https://', $redirectResponse->getTargetUrl());
        $this->assertEquals(302, $redirectResponse->headers->get('X-Status-Code'));
    }

    protected function fixKernelEventMock($event)
    {
        Phake::when($event)->getKernel()->thenReturn($this->kernel);
        Phake::when($event)->getRequest()->thenReturn($this->request);
        Phake::when($event)->getRequestType()->thenReturn(HttpKernelInterface::MASTER_REQUEST);
    }
}
