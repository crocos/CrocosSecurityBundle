<?php

namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Exception\AuthException;

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
     * Get security context.
     *
     * @return SecurityContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Check security.
     *
     * @param SecurityContext $context
     * @param object $object
     * @param string $method
     * @return string Forwarding cotroller
     *
     * @throws \LogicException If forwarding controller is unconfigured
     */
    public function authenticate(SecurityContext $context, $_object, $_method)
    {
        $object = new \ReflectionObject($_object);
        $method = $object->getMethod($_method);

        $this->loader->load($context, $object, $method);

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
