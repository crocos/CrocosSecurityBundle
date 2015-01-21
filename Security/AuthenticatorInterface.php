<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Crocos\SecurityBundle\Exception\AuthException;

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
     * @param boolean $enabled
     */
    public function enableHttpsRequiring($enabled);
}
