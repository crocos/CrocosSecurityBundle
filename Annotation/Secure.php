<?php

namespace Crocos\SecurityBundle\Annotation;

/**
 * @Secure annotation.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 *
 * @Annotation
 */
class Secure extends Annotation
{
    protected $disabled = false;
    protected $roles;

    /**
     * @return boolean
     */
    public function disabled()
    {
        return $this->disabled;
    }

    /**
     * @return array
     */
    public function roles()
    {
        return $this->roles;
    }
}
