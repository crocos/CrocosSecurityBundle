<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;

/**
 * @Secure(disabled=true, forward="Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction")
 */
class UserController
{
    /**
     * @Secure
     */
    public function securedAction()
    {
    }

    public function publicAction()
    {
    }

    public function loginAction()
    {
    }
}
