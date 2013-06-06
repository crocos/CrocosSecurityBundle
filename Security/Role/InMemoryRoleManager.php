<?php

namespace Crocos\SecurityBundle\Security\Role;

/**
 * InMemoryRoleManager.
 *
 * @author Toshiyuki Fujita <tofujiit@crocos.co.jp>
 */
class InMemoryRoleManager implements RoleManagerInterface
{
    protected $domain;
    protected $attributes = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = array();
    }

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
     * Set attribute to memory.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setAttribute($key, $value)
    {
        $this->attributes[$this->domain][$key] = $value;
    }

    /**
     * Get attribute from memory.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$this->domain][$key]) ?
            $this->attributes[$this->domain][$key] : $default;
    }
}
