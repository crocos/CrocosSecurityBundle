<?php
namespace Crocos\SecurityBundle\Security;

interface AuthorizerInterface
{
    /**
     * Authorize.
     *
     * @param SecurityContext $context
     *
     * @throws AuthException If user doesn't authorized
     */
    public function authorize(SecurityContext $context);
}
