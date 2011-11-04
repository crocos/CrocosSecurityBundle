<?php

namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Annotation\Secure;

/**
 * SecurityContext.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SecurityContext
{
    protected $secure = false;
    protected $requiredRoles = array();
    protected $domain = 'default';
    protected $strategy;
    protected $forwardingController;
    protected $previousUrlHolder;

    /**
     * Set secure.
     *
     * @param boolean $secure
     */
    public function setSecure($security)
    {
        $this->secure = $security;
    }

    /**
     * Check is secure.
     *
     * @return boolean
     */
    public function isSecure()
    {
        return (bool)$this->secure;
    }

    /**
     * Set required roles.
     *
     * @param array $roles
     */
    public function setRequiredRoles(array $roles)
    {
        $this->requiredRoles = $roles;
    }

    /**
     * Get required roles.
     *
     * @return array
     */
    public function getRequiredRoles()
    {
        return $this->requiredRoles;
    }

    /**
     * Set security domain name.
     *
     * @param string domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Get security domain name.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Set authentication/authorization strategy.
     *
     * @param AuthStrategyInterface $strategy
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * Get authentication/authorization strategy.
     *
     * @return AuthStrategyInterface
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * Set login controller.
     *
     * @param mixed $controller
     */
    public function setForwardingController($controller)
    {
        $this->forwardingController = $controller;
    }

    /**
     * Get login controller.
     *
     * @return mixed
     */
    public function getForwardingController()
    {
        return $this->forwardingController;
    }

    /**
     * Log in.
     *
     * @param user $user
     */
    public function login($user)
    {
        $this->strategy->login($user);
    }

    /**
     * Log out.
     */
    public function logout()
    {
        $this->strategy->logout();
    }

    /**
     * Check is authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return $this->strategy->isAuthenticated();
    }

    /**
     * Get user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return $this->strategy->getUser();
    }

    /**
     * Set PreviousUrlHolder.
     *
     * @param PreviousUrlHolder $previousUrlHolder
     */
    public function setPreviousUrlHolder(PreviousUrlHolder $previousUrlHolder)
    {
        $this->previousUrlHolder = $previousUrlHolder;
    }

    /**
     * Get PreviousUrlHolder.
     *
     * @return PreviousUrlHolder.
     */
    public function getPreviousUrlHolder()
    {
        return $this->previousUrlHolder;
    }

    /**
     * @see PreviousUrlHolder
     */
    public function hasPreviousUrl()
    {
        return $this->previousUrlHolder->has();
    }

    /**
     * @see PreviousUrlHolder
     */
    public function setPreviousUrl($url)
    {
        $this->previousUrlHolder->set($url);
    }

    /**
     * @see PreviousUrlHolder
     */
    public function getPreviousUrl()
    {
        return $this->previousUrlHolder->get();
    }
}
