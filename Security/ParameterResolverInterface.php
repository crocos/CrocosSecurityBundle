<?php
namespace Crocos\SecurityBundle\Security;

/**
 * ParameterResolverInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface ParameterResolverInterface
{
    public function resolveValue($value);
}
