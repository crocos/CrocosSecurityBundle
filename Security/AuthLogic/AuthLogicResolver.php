<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

/**
 * AuthLogicResolver.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AuthLogicResolver
{
    /**
     * @var array
     */
    protected $authLogics = [];

    /**
     * Register auth logic.
     *
     * @param string             $name
     * @param AuthLogicInterface $authLogic
     */
    public function registerAuthLogic($name, AuthLogicInterface $authLogic)
    {
        $this->authLogics[$name] = $authLogic;
    }

    /**
     * Resolve auth logic by name.
     *
     * @param  string             $name
     * @return AuthLogicInterface
     *
     * @throws \InvalidArgumentException If no auth logic matched given name
     */
    public function resolveAuthLogic($name)
    {
        if (!isset($this->authLogics[$name])) {
            throw new \InvalidArgumentException(sprintf('No auth logic registered "%s"', $name));
        }

        return $this->authLogics[$name];
    }
}
