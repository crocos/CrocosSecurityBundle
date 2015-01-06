<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\HttpAuth\BasicAuthFactory;

class BasicAuthFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function getGetName()
    {
        $factory = new BasicAuthFactory();

        $this->assertEquals('name', $factory);
    }

    public function testCreate()
    {
        $factory = new BasicAuthFactory();

        $basicAuth = $factory->create('foo:foopass', 'secured');

        $this->assertInstanceOf('Crocos\SecurityBundle\Security\HttpAuth\BasicAuth', $basicAuth);
        $this->assertEquals('Secured Area', $basicAuth->getRealm());
    }
}
