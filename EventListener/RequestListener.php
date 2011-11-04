<?php

namespace Crocos\SecurityBundle\EventListener;

use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Crocos\SecurityBundle\Security\SecurityChecker;

/**
 * RequestListener.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class RequestListener
{
    /**
     * @var SecurityChecker
     */
    protected $checker;

    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * Constructor.
     *
     * @param SecurityChecker $checker
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(SecurityChecker $checker, ControllerResolverInterface $resolver)
    {
        $this->checker = $checker;
        $this->resolver = $resolver;
    }

    /**
     * onKernelRequest.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $controller = $this->resolver->getController($request);

        //無駄になるけどRequestDataCollectorでこけるから...
        //$request->attributes->set('_controller', $controller);

        if (!is_array($controller)) {
            return;
        }

        $forwardingController = $this->checker->checkSecurity($controller[0], $controller[1]);
        if (null === $forwardingController) {
            return;
        }

        $this->checker->getContext()->setPreviousUrl($request->getUri());

        $response = $event->getKernel()->forward($forwardingController);

        $event->setResponse($response);
    }
}