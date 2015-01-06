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
     * @return string
     */
    public function getName();

    /**
     * @param  string            $value
     * @param  string            $domain
     * @return HttpAuthInterface
     */
    public function create($value, $domain);
}
