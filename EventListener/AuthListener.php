<?php
namespace Crocos\SecurityBundle\EventListener;

use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;
use Crocos\SecurityBundle\Security\AuthenticatorInterface;
use Crocos\SecurityBundle\Security\AuthorizerInterface;
use Crocos\SecurityBundle\Security\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

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
     * @var HttpKernelInterface
     */
    protected $httpKernel;

    /**
     * Constructor.
     *
     * @param SecurityContext             $context
     * @param AuthenticatorInterface      $authenticator
     * @param AuthorizerInterface         $authorizer
     * @param ControllerResolverInterface $resolver
     * @param HttpKernelInterface         $httpKernel
     */
    public function __construct(
        SecurityContext $context,
        AuthenticatorInterface $authenticator,
        AuthorizerInterface $authorizer,
        ControllerResolverInterface $resolver,
        HttpKernelInterface $httpKernel
    ) {
        $this->context = $context;
        $this->authenticator = $authenticator;
        $this->authorizer = $authorizer;
        $this->resolver = $resolver;
        $this->httpKernel = $httpKernel;
    }

    /**
     * Listen to the kernel.request event.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $controller = $this->resolver->getController($request);

        if (!is_array($controller)) {
            return;
        }

        // If not authenticated, will be thrown an AuthException
        $this->authenticator->authenticate($this->context, $controller, $request);
        $this->authorizer->authorize($this->context);
    }

    /**
     * Listen to the kernel.exception event.
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

        $response = $this->respondForAuthException($request, $exception);

        if (null !== $response) {
            $response->headers->set('X-Status-Code', $response->getStatusCode());
            $event->setResponse($response);
        }
    }

    /**
     * @param Request       $request
     * @param AuthException $exception
     *
     * @return Response
     */
    protected function respondForAuthException(Request $request, AuthException $exception)
    {
        // Switch http to https
        if ($exception instanceof HttpsRequiredException) {
            $sslUrl = preg_replace('_^http:_', 'https:', $request->getUri());

            return new RedirectResponse($sslUrl);
        }

        // Handle http auth
        if ($exception instanceof HttpAuthException) {
            return $this->context->getHttpAuth($exception->getName())->createUnauthorizedResponse($request, $exception);
        }

        // Forward to another controller
        $forwardingController = $this->context->getForwardingController();
        if (null === $forwardingController) {
            throw new \LogicException('You must configure "forward" attribute in @SecureConfig annotation');
        }

        // Keep previous url before forward
        $this->context->setPreviousUrl($request->getUri());

        return $this->forward($request, $forwardingController, $exception);
    }

    /**
     * @param Request       $request
     * @param string        $forwardingController
     * @param AuthException $exception
     *
     * @return Response
     */
    protected function forward(Request $request, $forwardingController, AuthException $exception)
    {
        $path = $exception->getAttributes();
        $path['_controller'] = $forwardingController;

        $subRequest = $request->duplicate($request->query->all(), null, $path);

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
