<?php

namespace Crocos\SecurityBundle\Exception;

/**
 * AuthException.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthException extends \RuntimeException
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * Constructor.
     *
     * @param string     $message
     * @param array      $attributes
     * @param integer    $code
     * @param \Exception $previous
     */
    public function __construct($message = '', array $attributes = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->attributes = $attributes;
    }

    /**
     * Get attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
