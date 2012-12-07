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
     * @param array $roles
     */
    function setRoles($roles);

    /**
     * @return array $roles
     */
    function getRoles();
}
