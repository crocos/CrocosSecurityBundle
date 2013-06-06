<?php

namespace Crocos\SecurityBundle\Security\Role;

/**
 * AbstractRoleManager.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
abstract class AbstractRoleManager implements RoleManagerInterface
{
    /**
     * @var string $domain
     */
    protected $domain;

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritDoc}
     */
    public function hasRole($roles)
    {
        if (!is_array($roles)) {
            $roles = array($roles);
        }

        if (count($roles) === 0) {
            return true;
        }

        $grantedRoles = $this->getRoles();

        if (count(array_intersect($roles, $grantedRoles)) === 0) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function setRoles($roles)
    {
        $this->setAttribute('roles', (array)$roles);
    }

    /**
     * {@inheritDoc}
     */
    public function addRoles($roles)
    {
        $roles = array_unique(array_merge($this->getRoles(), (array)$roles), SORT_STRING);
        $this->setRoles($roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->getAttribute('roles', array());
    }

    /**
     * {@inheritDoc}
     */
    public function clearRoles()
    {
        $this->setAttribute('roles', array());
        $this->setAttribute('preloaded', false);
    }

    /**
     * {@inheritDoc}
     */
    public function isPreloaded()
    {
        return $this->getAttribute('preloaded', false);
    }

    /**
     * {@inheritDoc}
     */
    public function setPreloaded($preloaded = true)
    {
        $this->setAttribute('preloaded', (bool)$preloaded);
    }

    /**
     * Set attribute.
     *
     * @param string $key
     * @param mixed $value
     */
    abstract protected function setAttribute($key, $value);

    /**
     * Get attribute.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    abstract protected function getAttribute($key, $default = null);
}
