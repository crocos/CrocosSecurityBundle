<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\EventListener\RequestListener;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class RequestListenerTest extends \PHPUnit_Framework_TestCase
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
        $checker = Phake::mock('Crocos\SecurityBundle\Security\SecurityChecker');

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($checker)->getContext()->thenReturn($context);

        $request = Request::create('/');

        $resolver = Phake::mock('Symfony\Component\HttpKernel\Controller\ControllerResolverInterface');

        $kernel = Phake::mock('Symfony\Bundle\FrameworkBundle\HttpKernel');

        $event = Phake::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        Phake::when($event)->getKernel()->thenReturn($kernel);
        Phake::when($event)->getRequest()->thenReturn($request);
        Phake::when($event)->getRequestType()->thenReturn(HttpKernelInterface::MASTER_REQUEST);

        $controller = array(new Fixtures\AdminController(), 'securedAction');
        Phake::when($resolver)->getController($request)->thenReturn($controller);

        $this->checker = $checker;
        $this->context = $context;
        $this->request = $request;
        $this->resolver = $resolver;
        $this->kernel = $kernel;
        $this->event = $event;
        $this->controller = $controller;
    }

    public function testHandleSecureAction()
    {
        $forwardingController = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        Phake::when($this->checker)->checkSecurity($this->controller[0], $this->controller[1])->thenReturn($forwardingController);

        $response = new Response('test');
        Phake::when($this->kernel)->forward($forwardingController)->thenReturn($response);

        $listener = new RequestListener($this->checker, $this->resolver);
        $listener->onKernelRequest($this->event);

        Phake::verify($this->context)->setPreviousUrl($this->request->getUri());
        Phake::verify($this->kernel)->forward($forwardingController);
        Phake::verify($this->event)->setResponse($response);
    }

    public function testHandleNotSecureAction()
    {
        $forwardingController = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        Phake::when($this->checker)->checkSecurity($this->controller[0], $this->controller[1])->thenReturn(null);

        Phake::verifyNoInteraction($this->kernel);

        $listener = new RequestListener($this->checker, $this->resolver);
        $listener->onKernelRequest($this->event);
    }
}
