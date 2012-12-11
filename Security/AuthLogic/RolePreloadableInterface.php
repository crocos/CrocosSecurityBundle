<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

/**
 * RolePreloadableInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface RolePreloadableInterface
{
    /**
     * Is role preloadable?
     *
     * @return boolean
     */
    function isRolePreloadable();

    /**
     * Preload roles.
     *
     * @return array
     */
    function preloadRoles();
}
