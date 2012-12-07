<?php

namespace Crocos\SecurityBundle\Security\Role\Facebook;

/**
 * RoleLoaderInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface RoleLoaderInterface
{
    /**
     * Load roles from Facebook with Graph API or else.
     *
     * @param \BaceFacebook $facebook
     * @param array $definedRoles
     */
    function loadRoles(\BaseFacebook $facebook, array $definedRoles);
}
