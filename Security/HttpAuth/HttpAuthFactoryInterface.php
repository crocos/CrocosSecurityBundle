<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

/**
 * HttpAuthFactoryInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface HttpAuthFactoryInterface
{
    /**
     * @param string $type
     * @param string $value
     * @param string $domain
     * @return HttpAuthInterface
     */
    function create($type, $value, $domain);
}
