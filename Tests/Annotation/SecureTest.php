<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Crocos\SecurityBundle\Annotation\Secure;
use Phake;

class SecureTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $secure = new Secure(array('disabled' => true));

        $this->assertTrue($secure->disabled());
    }

    public function testDisabledByDefault()
    {
        $secure = new Secure(array());
        $this->assertFalse($secure->disabled());
    }
}
