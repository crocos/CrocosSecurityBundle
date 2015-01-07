<?php

namespace Crocos\SecurityBundle\Annotation;

/**
 * Annotation.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
abstract class Annotation
{
    protected static $extendedAttrs = [];

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
        if (isset(self::$extendedAttrs[get_called_class()]) && array_key_exists($property, self::$extendedAttrs[get_called_class()])) {
            $this->{$property} = $value;

            return;
        }

        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __set.', $property));
    }

    /**
     * Disabled __get.
     */
    public function __get($property)
    {
        if (isset(self::$extendedAttrs[get_called_class()]) && array_key_exists($property, self::$extendedAttrs[get_called_class()])) {
            return isset($this->{$property}) ? $this->{$property} : self::$extendedAttrs[get_called_class()][$property];
        }

        throw new \BadMethodCallException(sprintf('Unknown property "%s" given to __get.', $property));
    }

    public function __call($method, $args)
    {
        try {
            return $this->{$method};
        } catch (\BadMethodCallException $e) {
            throw new \BadMethodCallException(sprintf('Unknown method "%s".', $method));
        }
    }

    public static function extendAttrs($attrs)
    {
        $cls = get_called_class();
        if (isset(self::$extendedAttrs[$cls])) {
            $attrs = array_merge(self::$extendedAttrs[$cls], $attrs);
        }

        self::$extendedAttrs[$cls] = $attrs;
    }
}
