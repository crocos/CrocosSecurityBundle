<?php

namespace Crocos\SecurityBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * AbstrctHttpException.
 *
 * @author Yuichi Okada <okada@crocos.co.jp>
 */
class AbstrctHttpException extends \RuntimeException implements HttpExceptionInterface
{
    /**
     * @var array
     */
    protected $attributes;

    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers;

    /**
     * Constructor.
     *
     * @param string $message
     * @param array $attributes
     * @param integer $statusCode
     * @param array $headers
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($message = '', array $attributes = [], $statusCode = 200, array $headers = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->attributes = $attributes;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
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

    /**
     * {@inheritDoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
