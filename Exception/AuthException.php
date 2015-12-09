<?php
namespace Crocos\SecurityBundle\Exception;

class AuthException extends \RuntimeException
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param string     $message
     * @param array      $attributes
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($message = '', array $attributes = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
