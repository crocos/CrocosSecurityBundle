<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy;

/**
 * FacebookAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class FacebookAuth implements AuthStrategyInterface
{
    protected $facebook;

    public function __construct(\BaseFacebook $facebook)
    {
        $this->facebook = $facebook;
    }

    public function setDomain($domain)
    {
    }

    public function login($user)
    {
    }

    public function logout()
    {
    }

    public function isAuthenticated()
    {
        return (bool)$this->facebook->getUser();
    }

    public function getUser()
    {
        return $this->facebook->getUser();
    }
}
