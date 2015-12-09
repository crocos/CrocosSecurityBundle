<?php
namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @SecureConfig(forward="Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction")
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

    /**
     * @SecureConfig(httpsRequired=true)
     */
    public function loginAction()
    {
    }
}
