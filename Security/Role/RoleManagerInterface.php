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
    public function setDomain($domain);

    /**
     * @param array|string $roles
     */
    public function hasRole($roles);

    /**
     * @param array $roles
     */
    public function setRoles($roles);

    /**
     * @param array $roles
     */
    public function addRoles($roles);

    /**
     * @return array $roles
     */
    public function getRoles();

    /**
     * Clear roles.
     */
    public function clearRoles();

    /**
     * Is role preloaded?
     *
     * @return bool
     */
    public function isPreloaded();

    /**
     * Set preloaded flag.
     *
     * @param bool preloaded$
     */
    public function setPreloaded($preloaded = true);
}
