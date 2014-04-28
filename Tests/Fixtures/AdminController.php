<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure(allow={"admin"})
 * @SecureConfig(roleManager="in_memory", httpsRequired=true)
 */
class AdminController extends SecureController
{
    public function securedAction()
    {
    }

    /**
     * @SecureConfig(httpsRequired=false)
     * @Secure(disabled=true)
     */
    public function publicAction()
    {
    }

    public function loginAction()
    {
    }

    public function forward()
    {
    }
}
