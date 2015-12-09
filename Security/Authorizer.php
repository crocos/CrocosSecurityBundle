<?php
namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Exception\AuthException;

/**
 * Authorizer.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class Authorizer implements AuthorizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function authorize(SecurityContext $context)
    {
        // authorize
        if (!$context->hasAllowedRoles()) {
            throw new AuthException('Access not allowed');
        }
    }
}
