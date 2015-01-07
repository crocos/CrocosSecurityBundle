<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

/**
 * BasicAuthFactory.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class BasicAuthFactory implements HttpAuthFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'basic';
    }

    /**
     * {@inheritDoc}
     */
    public function create($values, $domain)
    {
        if (!is_array($values)) {
            $values = (array) $values;
        }

        $users = [];
        foreach ($values as $value) {
            $secret = explode(':', $value);
            if (count($secret) != 2) {
                throw new \InvalidArgumentException(sprintf('You must provide basic="user:pass" (%s given)', $value));
            }
            $users[$secret[0]] = $secret[1];
        }

        // domain="secured" -> realm="Secured Area"
        $realm = sprintf('%s Area', ucwords(str_replace('_', ' ', $domain)));

        $auth = new BasicAuth($users, $realm);

        return $auth;
    }
}
