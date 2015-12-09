<?php
namespace Crocos\SecurityBundle\Security\AuthLogic;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * SessionAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionAuth implements AuthLogicInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function login($user)
    {
        $this->session->migrate();

        $this->setAttribute('_authenticated', true);
        $this->setAttribute('_user', $this->sleepUser($user));

        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function logout()
    {
        $this->session->invalidate();

        $this->setAttribute('_authenticated', false);
        $this->setAttribute('_user', null);

        $this->user = null;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return $this->getAttribute('_authenticated', false);
    }

    /**
     * {@inheritdoc}
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
     * @param mixed  $value
     */
    protected function setAttribute($key, $value)
    {
        $acutualKey = $this->domain.'.'.$key;

        $this->session->set($acutualKey, $value);
    }

    /**
     * Get attribute from session.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getAttribute($key, $default = null)
    {
        $acutualKey = $this->domain.'.'.$key;

        return $this->session->get($acutualKey, $default);
    }

    /**
     * sleep user.
     *
     * @param mixed $user
     *
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
     *
     * @return mixed
     */
    protected function awakeUser($data)
    {
        return $data;
    }
}
