<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

use Crocos\SecurityBundle\Security\Role\Facebook\RoleLoaderInterface;
use Crocos\SecurityBundle\Security\SecureOptionsAcceptableInterface;

/**
 * FacebookAuth delegates auth logic to Facebook PHP SDK.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class FacebookAuth implements AuthLogicInterface, SecureOptionsAcceptableInterface, RolePreloadableInterface
{
    /**
     * @var \BaseFacebook
     */
    protected $facebook;

    /**
     * @var @options
     */
    protected $options;

    /**
     * @var array
     */
    protected $roleLoaders = [];

    /**
     * Constructor.
     *
     * @param \BaseFacebook $facebook
     */
    public function __construct(\BaseFacebook $facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * {@inheritDoc}
     */
    public function setDomain($domain)
    {
        // nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function login($user)
    {
        // nothing to do
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $this->facebook->destroySession();
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthenticated()
    {
        return (bool) $this->facebook->getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this->facebook->getUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setOptions($options)
    {
        $this->options = (array) $options;
    }

    /**
     * Register RoleLoader.
     *
     * @param string              $name
     * @param RoleLoaderInterface $loader
     */
    public function registerRoleLoader($name, RoleLoaderInterface $loader)
    {
        $this->roleLoaders[$name] = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function isRolePreloadable()
    {
        foreach ($this->roleLoaders as $name => $roleLoader) {
            if (isset($this->options[$name])) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function preloadRoles()
    {
        $roles = [];

        foreach ($this->roleLoaders as $name => $roleLoader) {
            if (isset($this->options[$name])) {
                $roles = array_merge($roles, $roleLoader->loadRoles($this->facebook, $this->options[$name]));
            }
        }

        return $roles;
    }
}
