<?php

namespace Crocos\SecurityBundle\Security;

/**
 * SecurityChecker.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SecurityChecker
{
    /**
     * @var SecurityContext
     */
    protected $context;

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
     * @param SecurityContext $context
     * @param AnnotationLoader $loader
     * @param ForwardingControllerMatcher $matcher
     */
    public function __construct(SecurityContext $context, AnnotationLoader $loader, ForwardingControllerMatcher $matcher)
    {
        $this->context = $context;
        $this->loader = $loader;
        $this->matcher = $matcher;
    }

    /**
     * Check security.
     *
     * @param object $object
     * @param string $method
     * @return string Forwarding cotroller
     */
    public function checkSecurity($_object, $_method)
    {
        $object = new \ReflectionObject($_object);
        $method = $object->getMethod($_method);

        $this->loader->load($this->context, $object, $method);

        if (!$this->context->isSecure() || $this->matcher->isForwardingController($this->context, $object, $method)) {
            return;
        }

        $user = $this->context->getUser();
        if ($user) {
            return;
        }

        $forwardingController = $this->context->getForwardingController();
        if (null === $forwardingController) {
            throw new \LogicException('You must configure "forward" attribute in @Secure annotation that will be used as a login controller.');
        }

        return $forwardingController;
    }

    /**
     * Check given controller is login controller.
     *
     * @param array $controller
     * @param string $loginControllerName
     * @return boolean
     */
    protected function isLoginController($controller, $loginControllerName)
    {
        list($loginController, $loginAction) = explode('::', $loginControllerName);

        if ($loginController === get_class($controller[0])
            && $loginAction === $controller[1]
        ) {
            return true;
        }

        return false;
    }
}
