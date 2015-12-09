<?php
namespace Crocos\SecurityBundle\Tests\Fixtures;

use Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface;
use Crocos\SecurityBundle\Security\AuthLogic\RolePreloadableInterface;
use Crocos\SecurityBundle\Security\SecureOptionsAcceptableInterface;

interface ComplexedAuthLogicInterface extends
    AuthLogicInterface,
    RolePreloadableInterface,
    SecureOptionsAcceptableInterface
{
}
