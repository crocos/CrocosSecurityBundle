<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure(allow={"superadmin"})
 */
class SuperAdminController extends AdminController
{
    public function superAction()
    {
    }

    /**
     * @Secure(allow={"hyperadmin"})
     */
    public function hyperAction()
    {
    }

    /**
     * @Secure(allow="hyper2admin")
     */
    public function hyper2Action()
    {
    }
}
