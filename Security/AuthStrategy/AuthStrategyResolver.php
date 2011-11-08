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

    /**
     * Register auth strategy.
     *
     * @param string $name
     * @param AuthStrategyInterface $strategy
     */
    public function registerAuthStrategy($name, AuthStrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
    }

    /**
     * Resolve auth strategy by name.
     *
     * @param string $name
     * @return AuthStrategyInterface
     *
     * @throws \InvalidArgumentException If no auth strategy matched given name
     */
    public function resolveAuthStrategy($name)
    {
        if (!isset($this->strategies[$name])) {
            throw new \InvalidArgumentException(sprintf('No auth strategy registered "%s"', $name));
        }

        return $this->strategies[$name];
    }
}
