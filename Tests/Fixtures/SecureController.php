<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure
 * @SecureConfig(domain="admin", forward="Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction")
 */
abstract class SecureController
{
}
