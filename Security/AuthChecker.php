<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;

/**
 * AuthChecker.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthChecker implements AuthCheckerInterface
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
     * @var boolean
     */
    protected $httpsRequiringEnabled;

    /**
     * Constructor.
     *
     * @param AnnotationLoader            $loader
     * @param ForwardingControllerMatcher $matcher
     */
    public function __construct(AnnotationLoader $loader, ForwardingControllerMatcher $matcher)
    {
        $this->loader = $loader;
        $this->matcher = $matcher;
    }

    /**
     * @param boolean $enabled
     */
    public function enableHttpsRequiring($enabled)
    {
        $this->httpsRequiringEnabled = $enabled;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(SecurityContext $context, $_object, $_method, Request $request = null)
    {
        $object = new \ReflectionObject($_object);
        $method = $object->getMethod($_method);

        $this->loader->load($context, $object, $method);

        if ($request !== null) {
            // https
            if ($this->httpsRequiringEnabled && $context->isHttpsRequired() && !$request->isSecure()) {
                throw new HttpsRequiredException('HTTPS is required');
            }

            // http auth
            if ($request && $context->useHttpAuth() && false === $context->getHttpAuth()->authenticate($request)) {
                throw new HttpAuthException('Authentication required');
            }
        }

        // non secure controller or forwarding controller
        if (!$context->isSecure() || $this->matcher->isForwardingController($context, $object, $method)) {
            return;
        }

        // authenticate
        if (!$context->isAuthenticated()) {
            throw new AuthException('Login required');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function authorize(SecurityContext $context)
    {
        // authorize
        if (!$context->hasRole($context->getAllowedRoles())) {
            throw new AuthException('Access not allowed');
        }
    }
}
