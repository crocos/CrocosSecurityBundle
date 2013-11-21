<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure(allow={"admin"})
 * @SecureConfig(roleManager="in_memory")
 */
class AdminController extends SecureController
{
    public function securedAction()
    {
    }

    /**
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
