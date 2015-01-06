<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Crocos\SecurityBundle\Exception\AuthException;

interface AuthCheckerInterface
{
    /**
     * Authenticate user.
     *
     * @param SecurityContext $context
     * @param object          $object
     * @param string          $method
     * @param Request         $request
     *
     * @throws AuthException If user doesn't authenticated
     */
    public function authenticate(SecurityContext $context, $_object, $_method, Request $request = null);

    /**
     * Authorize
     *
     * @param SecurityContext $context
     *
     * @throws AuthException If user doesn't authorized
     */
    public function authorize(SecurityContext $context);
}
