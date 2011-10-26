<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;

/**
 * @Secure(domain="admin", forward="Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction")
 */
abstract class SecureController
{
}
