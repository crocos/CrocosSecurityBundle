<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * PreviousUrlHolder.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class PreviousUrlHolder
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $url;

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
     * Set domain.
     *
     * @param string $domain
     */
    public function setup($domain)
    {
        $this->domain = $domain;

        $this->url = $this->session->get($this->getKey());
    }

    /**
     * Has url?
     *
     * @return boolean
     */
    public function has()
    {
        return isset($this->url);
    }

    /**
     * Set url.
     *
     * @param string $url
     */
    public function set($url)
    {
        $this->url = $url;

        $this->session->set($this->getKey(), $url);
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function get()
    {
         $this->session->remove($this->getKey());

         return $this->url;
    }

    /**
     * Get session key.
     *
     * @return string
     */
    protected function getKey()
    {
        return "{$this->domain}._previous_url";
    }
}
