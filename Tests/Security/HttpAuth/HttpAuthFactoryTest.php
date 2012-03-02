<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory;
use Phake;

class HttpAuthFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBasicAuth()
    {
        $factory = Phake::partialMock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');

        $factory->create('basic', 'foo:foopass', 'private');

        Phake::verify($factory)->createBasicAuth('foo:foopass', 'private');
    }

    public function testReturnNullIfEmptyValueGiven()
    {
        $factory = Phake::partialMock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');

        $factory->create('basic', false, 'private');
        $factory->create('basic', null, 'private');

        Phake::verify($factory, Phake::times(0))->createBasicAuth('foo:foopass', 'private');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknown()
    {
        $factory = new HttpAuthFactory();

        $factory->create('wozozo', 'foo:foopass', 'private');
    }
}
