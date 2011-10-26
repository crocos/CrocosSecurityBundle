<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;

class AdminController extends SecureController
{
    public function securedAction()
    {
    }

    /**
     * @Secure(disabled = true)
     */
    public function publicAction()
    {
    }

    /**
     * @Secure(roles={"admin"})
     */
    public function adminAction()
    {
    }

    public function loginAction()
    {
    }
}
