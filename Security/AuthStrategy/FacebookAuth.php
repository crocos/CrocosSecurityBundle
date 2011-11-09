<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy;

/**
 * FacebookAuth delegates auth strategy to Facebook PHP SDK.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class FacebookAuth implements AuthStrategyInterface
{
    protected $facebook;

    /**
     * Constructor.
     *
     * @param \BaseFacebook $facebook
     */
    public function __construct(\BaseFacebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain($domain)
    {
        // nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function login($user)
    {
        // nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $this->facebook->destroySession();
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthenticated()
    {
        return (bool)$this->facebook->getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this->facebook->getUser();
    }
}
