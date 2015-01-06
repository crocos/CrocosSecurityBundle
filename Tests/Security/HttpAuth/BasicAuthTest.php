<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\Security\HttpAuth\BasicAuth;
use Crocos\SecurityBundle\Exception\HttpAuthException;

class BasicAuthTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthenticate()
    {
        $basicAuth = new BasicAuth(['foo' => 'foopass'], 'Secured Area');

        $this->assertTrue($basicAuth->authenticate($this->request('foo', 'foopass')));
    }

    public function testUnauthenticateInvalidUser()
    {
        $basicAuth = new BasicAuth(['foo' => 'foopass'], 'Secured Area');

        $this->assertFalse($basicAuth->authenticate($this->request('wozozo', 'foopass')));
    }

    public function testUnauthenticateInvalidPassword()
    {
        $basicAuth = new BasicAuth(['foo' => 'foopass'], 'Secured Area');

        $this->assertFalse($basicAuth->authenticate($this->request('foo', 'wozozo')));
    }

    public function testAuthenticateMultiUsers()
    {
        $basicAuth = new BasicAuth(['foo' => 'foopass', 'bar' => 'barpass'], 'Secured Area');

        $this->assertTrue($basicAuth->authenticate($this->request('foo', 'foopass')));
        $this->assertTrue($basicAuth->authenticate($this->request('bar', 'barpass')));
    }

    public function testCreateUnauthorizedResponse()
    {
        $basicAuth = new BasicAuth(['foo' => 'foopass'], 'Secured Area');

        $response = $basicAuth->createUnauthorizedResponse($this->request('foo', 'foopass'), new HttpAuthException('Auth error'));

        $this->assertEquals('Basic realm="Secured Area"', $response->headers->get('WWW-Authenticate'));
        $this->assertEquals(401, $response->getStatusCode());
    }

    protected function request($user = null, $pass = null)
    {
        $request = Request::create('/');

        $request->server->set('PHP_AUTH_USER', $user);
        $request->server->set('PHP_AUTH_PW', $pass);

        return $request;
    }
}
