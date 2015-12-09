<?php
namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Exception\AuthException;
use Crocos\SecurityBundle\Exception\HttpAuthException;
use Crocos\SecurityBundle\Exception\HttpsRequiredException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Authenticator.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class Authenticator implements AuthenticatorInterface
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
     * @var bool
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
     * {@inheritdoc}
     */
    public function enableHttpsRequiring($enabled)
    {
        $this->httpsRequiringEnabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(SecurityContext $context, $controller, Request $request = null)
    {
        if (!is_array($controller) || count($controller) !== 2) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        $this->loader->load($context, $object, $method);

        if ($request !== null) {
            // https
            if ($this->httpsRequiringEnabled && $context->isHttpsRequired() && !$request->isSecure()) {
                throw new HttpsRequiredException('HTTPS is required');
            }

            // http auth
            if ($request && $context->useHttpAuth()) {
                foreach ($context->getHttpAuths() as $name => $httpAuth) {
                    if ($httpAuth->authenticate($request) === false) {
                        throw new HttpAuthException($name, sprintf('HTTP Authentication required "%s"', $name));
                    }
                }
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
}
