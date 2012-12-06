<?php

namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface;

/**
 * SecurityContext.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SecurityContext
{
    /**
     * @var boolean
     */
    protected $secure = false;

    /**
     * @var array
     */
    protected $allowedRoles = array();

    /**
     * @var string
     */
    protected $domain = 'secured';

    /**
     * @var string
     */
    protected $forwardingController;

    /**
     * @var AuthLogicInterface
     */
    protected $authLogic;

    /**
     * @var PreviousUrlHolder
     */
    protected $previousUrlHolder;

    /**
     * @var HttpAuthInterface
     */
    protected $httpAuth;

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
    public function setAllowedRoles(array $roles)
    {
        $this->allowedRoles = $roles;
    }

    /**
     * Get required roles.
     *
     * @return array
     */
    public function getAllowedRoles()
    {
        return $this->allowedRoles;
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
     * Set authentication/authorization logic.
     *
     * @param AuthLogicInterface $authLogic
     */
    public function setAuthLogic($authLogic)
    {
        $this->authLogic = $authLogic;
    }

    /**
     * Get authentication/authorization logic.
     *
     * @return AuthLogicInterface
     */
    public function getAuthLogic()
    {
        return $this->authLogic;
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
        if (null === $this->authLogic) {
            throw new \LogicException('Login error: No auth logic');
        }

        $this->authLogic->login($user);
    }

    /**
     * Log out.
     */
    public function logout()
    {
        if (null === $this->authLogic) {
            throw new \LogicException('Logout error: No auth logic');
        }

        $this->authLogic->logout();
    }

    /**
     * Check is authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        if (null === $this->authLogic) {
            return false;
        }

        return $this->authLogic->isAuthenticated();
    }

    /**
     * Get user.
     *
     * @return mixed
     */
    public function getUser()
    {
        if (null === $this->authLogic) {
            return null;
        }

        return $this->authLogic->getUser();
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
        if (null === $this->previousUrlHolder) {
            return false;
        }

        return $this->previousUrlHolder->has();
    }

    /**
     * @see PreviousUrlHolder
     */
    public function setPreviousUrl($url)
    {
        if (null === $this->previousUrlHolder) {
            return null;
        }

        $this->previousUrlHolder->set($url);
    }

    /**
     * @see PreviousUrlHolder
     */
    public function getPreviousUrl()
    {
        if (null === $this->previousUrlHolder) {
            return null;
        }

        return $this->previousUrlHolder->get();
    }

    /**
     * Use http auth.
     *
     * @return boolean
     */
    public function useHttpAuth()
    {
        return (null !== $this->httpAuth);
    }

    /**
     * Set http auth.
     *
     * @param HttpAuthInterface $httpAuth
     */
    public function setHttpAuth(HttpAuthInterface $httpAuth = null)
    {
        $this->httpAuth = $httpAuth;
    }

    /**
     * Get http auth.
     *
     * @return HttpAuthInterface
     */
    public function getHttpAuth()
    {
        return $this->httpAuth;
    }
}
