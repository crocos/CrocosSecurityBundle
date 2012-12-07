<?php

namespace Crocos\SecurityBundle\Security\Role\Facebook;

/**
 * GroupRoleLoader.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class GroupRoleLoader
{
    /**
     * {@inheritDoc}
     */
    public function loadRoles(\BaseFacebook $facebook, array $definedRoles)
    {
        $user = $facebook->getUser();
        if (!$user) {
            return array();
        }

        try {
            $result = $facebook->api("{$user}/groups");
        } catch (\FacebookApiException $e) {
            return array();
        }

        $roles = array();
        if (isset($result['data'])) {
            foreach ($result['data'] as $group) {
                if (isset($definedRoles[$group['id']])) {
                    $roles[] = $definedRoles[$group['id']];
                }
            }
        }

        return $roles;
    }
}
