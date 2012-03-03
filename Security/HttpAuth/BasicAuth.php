<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\Exception\HttpAuthException;

/**
 * Basic http auth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class BasicAuth implements HttpAuthInterface
{
    /**
     * @var arry
     */
    protected $users;

    /**
     * @var string
     */
    protected $realm;

    /**
     * Constructor.
     *
     * @param array $users
     * @param string $realm
     */
    public function __construct(array $users, $realm)
    {
        $this->users = $users;
        $this->realm = $realm;
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(Request $request)
    {
        $user = $request->server->get('PHP_AUTH_USER');
        $pass = $request->server->get('PHP_AUTH_PW');

        if (isset($this->users[$user]) && $this->users[$user] === $pass) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function createUnauthorizedResponse(Request $request, HttpAuthException $exception)
    {
        $response = new Response();
        $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
        $response->setStatusCode(401);

        return $response;
    }

    /**
     * Get realm.
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }
}
