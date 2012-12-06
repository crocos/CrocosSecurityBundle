<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Request;

interface AuthCheckerInterface
{
    /**
     * Check security.
     *
     * @param SecurityContext $context
     * @param object $object
     * @param string $method
     * @param Request $request
     * @return string Forwarding cotroller
     */
    function authenticate(SecurityContext $context, $_object, $_method, Request $request = null);
}
