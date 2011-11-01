<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy;

use Symfony\Component\HttpFoundation\Session;

/**
 * SessionAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionAuth implements AuthStrategyInterface
{
    protected $session;
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
     * {@inheritDoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritDoc}
     */
    public function login($user)
    {
        $this->session->migrate();

        $this->setAttribute('_authenticated', true);
        $this->setAttribute('_user', $this->sleepUser($user));
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $this->session->invalidate();

        $this->setAttribute('_authenticated', false);
        $this->setAttribute('_user', null);
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthenticated()
    {
        return $this->getAttribute('_authenticated', false);
    }

    /**
     * {@inheritDoc}
     */
    public function getUser()
    {
        return $this->awakeUser($this->getAttribute('_user'));
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

    /**
     * sleep user.
     *
     * @param mixed $user
     * @return mixed sleepd data.
     */
    protected function sleepUser($user)
    {
        return $user;
    }

    /**
     * Awake user from sleepd data.
     *
     * @param mixed $data sleepd data made by sleep()
     * @return mixed
     */
    protected function awakeUser($data)
    {
        return $data;
    }
}
