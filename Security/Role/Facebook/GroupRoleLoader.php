<?php

namespace Crocos\SecurityBundle\Security\Role\Facebook;

/**
 * GroupRoleLoader.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class GroupRoleLoader implements RoleLoaderInterface
{
    /**
     * {@inheritDoc}
     */
    public function loadRoles(\BaseFacebook $facebook, array $roleMappings)
    {
        $user = $facebook->getUser();
        if (!$user) {
            return array();
        }

        try {
            $result = $facebook->api("{$user}/groups");
        } catch (\FacebookApiException $e) {
            throw new \RuntimeException('Cannot fetch groups', 0, $e);
        }

        $roles = array();
        if (isset($result['data'])) {
            foreach ($result['data'] as $group) {
                if (isset($roleMappings[$group['id']])) {
                    $roles[] = $roleMappings[$group['id']];
                }
            }
        }

        return $roles;
    }
}
