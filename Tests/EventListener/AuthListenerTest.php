<?php

namespace Crocos\SecurityBundle\Tests\EventListener;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\EventListener\AuthListener;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AuthListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $checker;
    protected $context;
    protected $request;
    protected $resolver;
    protected $kernel;
    protected $event;
    protected $controller;

    protected function setUp()
    {
        $checker = Phake::mock('Crocos\SecurityBundle\Security\AuthChecker');

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');

        $request = Request::create('/');

        $resolver = Phake::mock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');

        $kernel = Phake::mock('Symfony\Bundle\FrameworkBundle\HttpKernel');

        $this->checker = $checker;
        $this->context = $context;
        $this->request = $request;
        $this->resolver = $resolver;
        $this->kernel = $kernel;
    }

    public function testHandleRequest()
    {
        $controller = array(new Fixtures\AdminController(), 'securedAction');
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $this->fixKernelEventMock($event);

        $listener = new AuthListener($this->context, $this->checker, $this->resolver);
        $listener->onKernelRequest($event);

        Phake::verify($this->checker)->authenticate($this->context, $controller[0], $controller[1], $this->request);
    }

    public function testHandleAuthException()
    {
        $controller = array(new Fixtures\AdminController(), 'securedAction');
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        $attrs = array('foo' => 'bar');
        $exception = new AuthException('error', $attrs);
        Phake::when($event)->getException()->thenReturn($exception);

        $forwardingController = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        Phake::when($this->context)->getForwardingController()->thenReturn($forwardingController);

        $response = new Response('test');
        Phake::when($this->kernel)->forward($forwardingController, $attrs)->thenReturn($response);

        $listener = new AuthListener($this->context, $this->checker, $this->resolver);
        $listener->onKernelException($event);

        Phake::verify($this->context)->setPreviousUrl($this->request->getUri());
        Phake::verify($event)->setResponse($response);
    }

    public function testHandleHttpAuthException()
    {
        $controller = array(new Fixtures\BasicSecurityController(), 'securedAction');
        Phake::when($this->resolver)->getController($this->request)->thenReturn($controller);

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent');
        $this->fixKernelEventMock($event);

        $exception = new HttpAuthException('error');
        Phake::when($event)->getException()->thenReturn($exception);

        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $response = new Response('Authentication required', 401);
        Phake::when($httpAuth)->createUnauthorizedResponse($this->request, $exception)->thenReturn($response);
        Phake::when($this->context)->useHttpAuth()->thenReturn(true);
        Phake::when($this->context)->getHttpAuth()->thenReturn($httpAuth);

        $listener = new AuthListener($this->context, $this->checker, $this->resolver);
        $listener->onKernelException($event);

        Phake::verify($httpAuth)->createUnauthorizedResponse($this->request, $exception);
        Phake::verify($event)->setResponse($response);
    }

    protected function fixKernelEventMock($event)
    {
        Phake::when($event)->getKernel()->thenReturn($this->kernel);
        Phake::when($event)->getRequest()->thenReturn($this->request);
        Phake::when($event)->getRequestType()->thenReturn(HttpKernelInterface::MASTER_REQUEST);
    }
}
