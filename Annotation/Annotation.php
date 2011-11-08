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
     * Disabled __set.
     */
    public function __set($property, $value)
    {
        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __set.', $property));
    }

    /**
     * Disabled __get.
     */
    public function __get($property)
    {
        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __get.', $property));
    }
}
