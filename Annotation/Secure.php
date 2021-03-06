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
    /**
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var array
     */
    protected $allow;

    /**
     * @return bool
     */
    public function disabled()
    {
        return $this->disabled;
    }

    /**
     * @return array
     */
    public function allow()
    {
        if (is_string($this->allow)) {
            $this->allow = (array) $this->allow;
        }

        return $this->allow;
    }
}
