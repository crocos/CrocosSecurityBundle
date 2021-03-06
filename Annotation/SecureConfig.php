<?php
namespace Crocos\SecurityBundle\Annotation;

/**
 * @SecureConfig annotation.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 *
 * @Annotation
 */
class SecureConfig extends Annotation
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $httpsRequired;

    /**
     * @var string
     */
    protected $auth;

    /**
     * @var string
     */
    protected $roleManager;

    /**
     * @var string
     */
    protected $forward;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * @return bool
     */
    public function httpsRequired()
    {
        return $this->httpsRequired;
    }

    /**
     * @return string
     */
    public function auth()
    {
        return $this->auth;
    }

    /**
     * @return string
     */
    public function roleManager()
    {
        return $this->roleManager;
    }

    /**
     * @return string
     */
    public function forward()
    {
        return $this->forward;
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options;
    }
}
