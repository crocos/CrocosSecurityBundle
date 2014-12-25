<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Crocos\SecurityBundle\Annotation\Secure;

class SecureTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $secure = new Secure(['disabled' => true]);

        $this->assertTrue($secure->disabled());
    }

    public function testDisabledByDefault()
    {
        $secure = new Secure([]);
        $this->assertFalse($secure->disabled());
    }
}
