<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure
 * @SecureConfig(domain="facebook", forward="Crocos\SecurityBundle\Tests\Fixtures\FacebookController::loginAction",
 *     auth="facebook", options={"group"={"10000001" = "ADMIN"}})
 */
class FacebookController
{
    public function securedAction()
    {
    }

    /**
     * @Secure(allow={"ADMIN"})
     */
    public function adminAction()
    {
    }

    public function loginAction()
    {
    }
}
