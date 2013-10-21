<?php

namespace Crocos\SecurityBundle\Security\Role;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * SessionRoleManager.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionRoleManager extends AbstractRoleManager
{
    /**
     * @var Session $session
     */
    protected $session;

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
    protected function setAttribute($key, $value)
    {
        $this->session->set($this->computeAttributeKey($key), $value);
    }

    /**
     * {@inheritDoc}
     */
    protected function getAttribute($key, $default = null)
    {
        return $this->session->get($this->computeAttributeKey($key), $default);
    }

    /**
     * @param string $key
     * @return string
     */
    protected function computeAttributeKey($key)
    {
        return "{$this->domain}/role/{$key}";
    }
}
