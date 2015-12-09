<?php
namespace Crocos\SecurityBundle\Exception;

class HttpAuthException extends AuthException
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @param string     $name
     * @param string     $message
     * @param array      $attributes
     * @param int        $code
     * @param \Exception $previous
     *
     * @see AuthException
     */
    public function __construct($name, $message = '', array $attributes = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $attributes, $code, $previous);

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
