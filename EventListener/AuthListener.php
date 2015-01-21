<?php

namespace Crocos\SecurityBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;
use Crocos\SecurityBundle\Security\AuthenticatorInterface;
use Crocos\SecurityBundle\Security\AuthorizerInterface;
use Crocos\SecurityBundle\Security\SecurityContext;

/**
 * AuthListener.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthListener
{
    /**
     * @var SecurityContext
     */
    protected $context;

    /**
     * @var AuthenticatorInterface
     */
    protected $authenticator;

    /**
     * @var AuthorizerInterface
     */
    protected $authorizer;

    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * Constructor.
     *
     * @param SecurityContext             $context
     * @param AuthenticatorInterface      $authenticator
     * @param AuthorizerInterface         $authorizer
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(
        SecurityContext $context,
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ControllerResolverInterface $resolver
    ) {
        $this->context = $context;
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
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

        // If not authenticated, will be thrown an AuthException
        $this->authenticator->authenticate($this->context, $controller, $request);
        $this->authorizer->authorize($this->context);
    }

    /**
     * onKernelException.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $exception = $event->getException();

        if (!$exception instanceof AuthException) {
            return;
        }

        $response = null;
        if ($exception instanceof HttpsRequiredException) {
            $sslUrl = preg_replace('_^http:_', 'https:', $request->getUri());

            $response = new RedirectResponse($sslUrl);
        } elseif ($exception instanceof HttpAuthException) {
            $response = $this->context->getHttpAuth($exception->getName())->createUnauthorizedResponse($request, $exception);
        } else {
            $forwardingController = $this->context->getForwardingController();
            if (null === $forwardingController) {
                throw new \LogicException('You must configure "forward" attribute in @SecureConfig annotation');
            }

            // Save actual url.
            $this->context->setPreviousUrl($request->getUri());

            $controller = $this->resolver->getController($request);
            $response = $controller[0]->forward($forwardingController, $exception->getAttributes(), $request->query->all());
        }

        if (null !== $response) {
            $response->headers->set('X-Status-Code', $response->getStatusCode());
            $event->setResponse($response);
        }
    }
}
