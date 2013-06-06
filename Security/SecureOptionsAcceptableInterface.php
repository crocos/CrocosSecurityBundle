<?php

namespace Crocos\SecurityBundle\Security;

/**
 * SecureOptionsAcceptableInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface SecureOptionsAcceptableInterface
{
    /**
     * @param array $options
     */
    function setOptions($options);
}
