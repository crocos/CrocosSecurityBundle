<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @SecureConfig(httpsRequired="%auth.https%", basic="%auth.basic%")
 */
class BasicSecurityController
{
    public function securedAction()
    {
    }
}
