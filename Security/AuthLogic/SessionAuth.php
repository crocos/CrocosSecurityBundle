<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * SessionAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionAuth implements AuthLogicInterface
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string $domain
     */
    protected $domain;

    /**
     * @var mixed
     */
    protected $user;

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

        $this->user = $user;
    }

    /**
     * {@inheritDoc}
     */
    public function logout()
    {
        $this->session->invalidate();

        $this->setAttribute('_authenticated', false);
        $this->setAttribute('_user', null);

        $this->user = null;
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
        if (!isset($this->user)) {
            try {
                $this->user = $this->awakeUser($this->getAttribute('_user'));
            } catch (\Exception $e) {
                $this->logout();

                throw $e;
            }
        }

        return $this->user;
    }

    /**
     * Set attribute to session.
     *
     * @param string $key
     * @param mixed $value
     */
    protected function setAttribute($key, $value)
    {
        $acutualKey = $this->domain . '.' . $key;

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
        $acutualKey = $this->domain . '.' . $key;

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
