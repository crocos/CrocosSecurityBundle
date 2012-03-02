<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory;
use Phake;

class HttpAuthFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicAuth()
    {
        $factory = Phake::partialMock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');

        $basicAuth = $factory->create('basic', 'foo:foopass', 'secured');

        $this->assertEquals('Secured Area', $basicAuth->getRealm());

        Phake::verify($factory)->createBasicAuth('foo:foopass', 'secured');
    }

    public function testReturnNullIfEmptyValueGiven()
    {
        $factory = Phake::partialMock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');

        $factory->create('basic', false, 'secured');
        $factory->create('basic', null, 'secured');

        Phake::verify($factory, Phake::times(0))->createBasicAuth('foo:foopass', 'secured');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknown()
    {
        $factory = new HttpAuthFactory();

        $factory->create('wozozo', 'foo:foopass', 'secured');
    }
}
