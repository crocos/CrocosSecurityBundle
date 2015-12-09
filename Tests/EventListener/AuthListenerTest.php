<?php
namespace Crocos\SecurityBundle\Tests\EventListener;

use Crocos\SecurityBundle\EventListener\AuthListener;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AuthListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $listener;
    protected $authenticator;
    protected $authorizer;
    protected $context;
    protected $request;
    protected $resolver;
    protected $httpKernel;
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

        $httpKernel = Phake::mock('Symfony\Component\HttpKernel\HttpKernelInterface');

        $listener = new AuthListener($context, $authenticator, $authorizer, $resolver, $httpKernel);

        $this->listener = $listener;
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->context = $context;
        $this->query = $query;
        $this->request = $request;
        $this->resolver = $resolver;
        $this->httpKernel = $httpKernel;
    }

    public function testHandleRequest()
    {
        $controller = $this->createController(new Fixtures\AdminController(), 'securedAction');

        $event = $this->createResponseEvent();

        $this->listener->onKernelRequest($event);

        Phake::verify($this->authenticator)->authenticate($this->context, $controller, $this->request);
        Phake::verify($this->authorizer)->authorize($this->context);
    }

    public function testHandleAuthException()
    {
        $controller = $this->createController(Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\AdminController'), 'securedAction');

        $event = $this->createResponseForExceptionEvent();

        $attrs = ['foo' => 'bar'];
        $exception = new AuthException('error', $attrs);
        Phake::when($event)->getException()->thenReturn($exception);

        $forwardingController = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        Phake::when($this->context)->getForwardingController()->thenReturn($forwardingController);

        $response = new Response('test');
        Phake::when($this->httpKernel)->handle(Phake::capture($subRequest), HttpKernelInterface::SUB_REQUEST)->thenReturn($response);

        $this->listener->onKernelException($event);

        Phake::verify($this->context)->setPreviousUrl($this->request->getUri());
        Phake::verify($event)->setResponse($response);
        $this->assertEquals(200, $response->headers->get('X-Status-Code'));
        $this->assertEquals($forwardingController, $subRequest->attributes->get('_controller'));
    }

    public function testHandleHttpAuthException()
    {
        $controller = $this->createController(new Fixtures\BasicSecurityController(), 'securedAction');

        $event = $this->createResponseForExceptionEvent();

        $exception = new HttpAuthException('basic', 'error');
        Phake::when($event)->getException()->thenReturn($exception);

        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $response = new Response('Authentication required', 401);
        Phake::when($httpAuth)->createUnauthorizedResponse($this->request, $exception)->thenReturn($response);
        Phake::when($this->context)->useHttpAuth()->thenReturn(true);
        Phake::when($this->context)->getHttpAuth('basic')->thenReturn($httpAuth);

        $this->listener->onKernelException($event);

        Phake::verify($httpAuth)->createUnauthorizedResponse($this->request, $exception);
        Phake::verify($event)->setResponse($response);
        $this->assertEquals(401, $response->headers->get('X-Status-Code'));
    }

    public function testHandleHttpsRequiredAuthException()
    {
        $controller = $this->createController(Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\AdminController'), 'securedAction');

        $event = $this->createResponseForExceptionEvent();

        $exception = new HttpsRequiredException();
        Phake::when($event)->getException()->thenReturn($exception);

        $this->listener->onKernelException($event);

        Phake::verify($event)->setResponse(Phake::capture($redirectResponse));

        $this->assertStringStartsWith('https://', $redirectResponse->getTargetUrl());
        $this->assertEquals(302, $redirectResponse->headers->get('X-Status-Code'));
    }

    protected function createController($class, $method)
    {
        $controller = [new Fixtures\AdminController(), 'securedAction'];
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        return $controller;
    }

    protected function createResponseEvent()
    {
        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $this->fixKernelEventMock($event);

        return $event;
    }

    protected function createResponseForExceptionEvent()
    {
        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        return $event;
    }

    protected function fixKernelEventMock($event)
    {
        Phake::when($event)->getKernel()->thenReturn($this->httpKernel);
        Phake::when($event)->getRequest()->thenReturn($this->request);
        Phake::when($event)->isMasterRequest()->thenReturn(true);
    }
}
