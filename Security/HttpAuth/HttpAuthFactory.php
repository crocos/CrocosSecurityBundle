<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

/**
 * HttpAuthFactory.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class HttpAuthFactory implements HttpAuthFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($type, $value, $domain)
    {
        if (null === $value || false === $value) {
            return;
        }

        if ('basic' === $type) {
            return $this->createBasicAuth($value, $domain);
        } else {
            throw new \InvalidArgumentException(sprintf('Unknown http auth "%s"', $type));
        }
    }

    /**
     * @param string|array $values
     * @param string $domain
     * @return BasicAuth
     */
    protected function createBasicAuth($values, $domain)
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $users = array();
        foreach ($values as $value) {
            $secret = explode(':', $value);
            if (count($secret) != 2) {
                throw new \InvalidArgumentException(sprintf('You must provide basic="user:pass" (%s given)', $value));
            }
            $users[$secret[0]] = $secret[1];
        }

        $realm = sprintf('%s Area', ucwords(str_replace('_', ' ', $domain)));

        $auth = new BasicAuth($users, $realm);

        return $auth;
    }
}
