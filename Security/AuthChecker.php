<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;

/**
 * AuthChecker.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthChecker
{
    /**
     * @var AnnotationLoader
     */
    protected $loader;

    /**
     * @var ForwardingControllerMatcher
     */
    protected $matcher;

    /**
     * Constructor.
     *
     * @param AnnotationLoader $loader
     * @param ForwardingControllerMatcher $matcher
     */
    public function __construct(AnnotationLoader $loader, ForwardingControllerMatcher $matcher)
    {
        $this->loader = $loader;
        $this->matcher = $matcher;
    }

    /**
     * Check security.
     *
     * @param SecurityContext $context
     * @param object $object
     * @param string $method
     * @param Request $request
     * @return string Forwarding cotroller
     *
     * @throws \LogicException If forwarding controller is unconfigured
     */
    public function authenticate(SecurityContext $context, $_object, $_method, Request $request = null)
    {
        $object = new \ReflectionObject($_object);
        $method = $object->getMethod($_method);

        $this->loader->load($context, $object, $method);

        // http auth
        if ($request && $context->useHttpAuth() && false === $context->getHttpAuth()->authenticate($request)) {
            throw new HttpAuthException('Authentication required');
        }

        // not secure
        if (!$context->isSecure() || $this->matcher->isForwardingController($context, $object, $method)) {
            return;
        }

        // authenticated
        if ($context->isAuthenticated()) {
            return;
        }

        throw new AuthException('Login required');
    }
}
