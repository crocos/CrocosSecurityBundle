<?php

namespace Crocos\SecurityBundle\Exception;

/**
 * HttpAuthException.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class HttpAuthException extends AuthException
{
    protected $name;

    public function __construct($name, $message = '', array $attributes = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $attributes, $code, $previous);

        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}
