<?php

namespace Crocos\SecurityBundle\Security\Role;

use Symfony\Component\HttpFoundation\Session;

/**
 * SessionRoleManager.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionRoleManager implements RoleManagerInterface
{
    protected $domain;

    /**
     * Constructor.
     *
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
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
    public function setRoles($roles)
    {
        $this->setAttribute('roles', $roles);
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return $this->getAttribute('roles', array());
    }

    /**
     * Set attribute to session.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setAttribute($key, $value)
    {
        $acutualKey = $this->domain . '/' . $key;

        $this->session->set($acutualKey, $value);
    }

    /**
     * Get attribute from session.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getAttribute($key, $default = null)
    {
        $acutualKey = $this->domain . '/' . $key;

        return $this->session->get($acutualKey, $default);
    }
}
