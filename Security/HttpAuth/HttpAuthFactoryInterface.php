<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

/**
 * HttpAuthFactoryInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface HttpAuthFactoryInterface
{
    const PRIORITY_HIGH = 20;
    const PRIORITY_MID  = 10;
    const PRIORITY_LOW  = 0;

    /**
     * @return string
     */
    public function getname();

    /**
     * @return integer
     */
    public function getPriority();

    /**
     * @param  string            $value
     * @param  string            $domain
     * @return HttpAuthInterface
     */
    public function create($value, $domain);
}
