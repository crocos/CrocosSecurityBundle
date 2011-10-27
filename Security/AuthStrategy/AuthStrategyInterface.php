<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy;

/**
 * AuthStrategyInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface AuthStrategyInterface
{
    /**
     * Set authentication domain.
     */
    function setDomain($domain);

    /**
     * Log in.
     *
     * @param user $user
     */
    function login($user);

    /**
     * Log out.
     */
    function logout();

    /**
     * Check is authenticated.
     *
     * @return boolean
     */
    function isAuthenticated();

    /**
     * Get user.
     *
     * @return mixed
     */
    function getUser();
}
