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
     * @var string
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
     * {@inheritdoc}
     */
    public function hasRole($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
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
     * {@inheritdoc}
     */
    public function setRoles($roles)
    {
        $this->setAttribute('roles', (array) $roles);
    }

    /**
     * {@inheritdoc}
     */
    public function addRoles($roles)
    {
        $roles = array_unique(array_merge($this->getRoles(), (array) $roles), SORT_STRING);
        $this->setRoles($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->getAttribute('roles', []);
    }

    /**
     * {@inheritdoc}
     */
    public function clearRoles()
    {
        $this->setAttribute('roles', []);
        $this->setAttribute('preloaded', false);
    }

    /**
     * {@inheritdoc}
     */
    public function isPreloaded()
    {
        return $this->getAttribute('preloaded', false);
    }

    /**
     * {@inheritdoc}
     */
    public function setPreloaded($preloaded = true)
    {
        $this->setAttribute('preloaded', (bool) $preloaded);
    }

    /**
     * Set attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    abstract protected function setAttribute($key, $value);

    /**
     * Get attribute.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    abstract protected function getAttribute($key, $default = null);
}
