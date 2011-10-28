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
    protected $domain;
    protected $strategy;
    protected $forward;

    /**
     * @return string
     */
    public function domain()
    {
        return $this->domain;
    }

    /**
     * @return string
     */
    public function strategy()
    {
        return $this->strategy;
    }

    /**
     * @return string
     */
    public function forward()
    {
        return $this->forward;
    }
}
