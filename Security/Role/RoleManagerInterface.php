<?php

namespace Crocos\SecurityBundle\Security\Role;

/**
 * RoleManagerInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface RoleManagerInterface
{
    /**
     * @param string $domain
     */
    function setDomain($domain);

    /**
     * @param array|string $roles
     */
    function hasRole($roles);

    /**
     * @param array $roles
     */
    function setRoles($roles);

    /**
     * @param array $roles
     */
    function addRoles($roles);

    /**
     * @return array $roles
     */
    function getRoles();

    /**
     * Clear roles.
     */
    function clearRoles();

    /**
     * Is role preloaded?
     *
     * @return boolean
     */
    function isPreloaded();

    /**
     * Set preloaded flag.
     *
     * @param boolean $bool
     */
    function setPreloaded($preloaded = true);
}
