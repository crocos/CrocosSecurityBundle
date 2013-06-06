<?php

namespace Crocos\SecurityBundle\Security\Role;

/**
 * RoleManagerResolver.
 *
 * @author Toshiyuki Fujita <tofujiit@crocos.co.jp>
 */
class RoleManagerResolver
{
    /**
     * @var array
     */
    protected $roleManagers = array();

    /**
     * Register role manager.
     *
     * @param string $name
     * @param RoleManagerInterface $roleManager
     */
    public function registerRoleManager($name, RoleManagerInterface $roleManager)
    {
        $this->roleManagers[$name] = $roleManager;
    }

    /**
     * Resolve role manager by name.
     *
     * @param string $name
     * @return RoleManagerInterface
     *
     * @throws \InvalidArgumentException If no role manager matched given name
     */
    public function resolveRoleManager($name)
    {
        if (!isset($this->roleManagers[$name])) {
            throw new \InvalidArgumentException(sprintf('No role manager registered "%s"', $name));
        }

        return $this->roleManagers[$name];
    }
}
