<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy\UserHibernator;

/**
 * SerializeHibernator.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SerializeHibernator implements UserHibernatorInterface
{
    public function hibernate($user)
    {
        return serialize($user);
    }

    public function awake($data)
    {
        return unserialize($data);
    }
}
