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
        $this->{$property} = $value;

        return;
    }

    /**
     * Disabled __get.
     */
    public function __get($property)
    {
        if (!isset($this->{$property})) {
            return;
        }

        return $this->{$property};
    }

    public function __call($method, $args)
    {
        return $this->{$method};
    }
}
