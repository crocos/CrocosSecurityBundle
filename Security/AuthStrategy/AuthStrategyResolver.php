<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy;

/**
 * AuthStrategyResolver.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthStrategyResolver
{
    protected $strategies = array();

    public function registerStrategy($name, AuthStrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
    }

    public function resolveAuthStrategy($name)
    {
        if (!isset($this->strategies[$name])) {
            throw new \InvalidArgumentException(sprintf('No auth strategy registered "%s"', $name));
        }

        return $this->strategies[$name];
    }
}
