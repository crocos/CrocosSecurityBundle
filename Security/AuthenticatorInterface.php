<?php
namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Exception\AuthException;
use Symfony\Component\HttpFoundation\Request;

interface AuthenticatorInterface
{
    /**
     * Authenticate user.
     *
     * @param SecurityContext $context
     * @param mixed           $controller
     * @param Request         $request
     *
     * @throws AuthException If user doesn't authenticated
     */
    public function authenticate(SecurityContext $context, $controller, Request $request = null);

    /**
     * @param bool $enabled
     */
    public function enableHttpsRequiring($enabled);
}
