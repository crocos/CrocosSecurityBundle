<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

/**
 * AuthLogicInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface AuthLogicInterface
{
    /**
     * Set authentication domain.
     */
    public function setDomain($domain);

    /**
     * Log in.
     *
     * @param user $user
     */
    public function login($user);

    /**
     * Log out.
     */
    public function logout();

    /**
     * Check is authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated();

    /**
     * Get user.
     *
     * @return mixed
     */
    public function getUser();
}
