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
    public function isRolePreloadable();

    /**
     * Preload roles.
     *
     * @return array
     */
    public function preloadRoles();
}
