<?php
namespace Crocos\SecurityBundle\Tests\Exception;

use Crocos\SecurityBundle\Exception\AuthException;

class AuthExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exception = new AuthException('Security error', [], 100, $previous = new \LogicException('dummy'));

        $this->assertEquals('Security error', $exception->getMessage());
        $this->assertEquals(100, $exception->getCode());
        $this->assertEquals($previous, $exception->getPrevious());
    }

    public function testIsInstanceOfRuntimeException()
    {
        $exception = new AuthException('Secuirty error');
        if (!$exception instanceof \RuntimeException) {
            $this->fail();
        }
    }

    public function testGetAttributes()
    {
        $exception = new AuthException('Secuirty error', ['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $exception->getAttributes());
    }
}
