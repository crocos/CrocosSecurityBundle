<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;

/**
 * @Secure
 * @SecureConfig(domain="admin", basic={"foo:foopass", "bar:barpass"})
 */
class MultiUserBasicSecuredController
{
}
