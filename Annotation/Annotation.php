<?php

namespace Crocos\SecurityBundle\Annotation;

/**
 * Annotation.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
abstract class Annotation
{
    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->{$key} = $value;
        }
    }

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

    /**
     * __set.
     */
    public function __set($property, $value)
    {
        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __set.', $property));
    }

    /**
     * __get.
     */
    public function __get($property)
    {
        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __get.', $property));
    }
}
