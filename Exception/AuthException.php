<?php

namespace Crocos\SecurityBundle\Exception;

/**
 * AuthException.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthException extends \RuntimeException
{
    protected $attributes;

    public function __construct($message = '', array $attributes = array(), $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->attributes = $attributes;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }
}
