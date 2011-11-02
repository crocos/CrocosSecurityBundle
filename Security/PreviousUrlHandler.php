<?php

namespace Crocos\SecurityBundle\Security;

use Symfony\Component\HttpFoundation\Session;

/**
 * PreviousUrlHandler.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class PreviousUrlHandler
{
    protected $session;
    protected $domain;
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
     *
     */
    public function has()
    {
        return isset($this->url);
    }

    /**
     *
     */
    public function set($url)
    {
        $this->url = $url;

        $this->session->set($this->getKey(), $url);
    }

    /**
     *
     */
    public function get()
    {
         $this->session->remove($this->getKey());

         return $this->url;
    }

    protected function getKey()
    {
        return "{$this->domain}._previous_url";
    }
}
