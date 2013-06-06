<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
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

    /**
     * @Secure(allow={"admin"})
     */
    public function adminAction()
    {
    }

    public function loginAction()
    {
    }
}
