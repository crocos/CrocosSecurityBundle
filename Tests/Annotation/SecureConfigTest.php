<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Crocos\SecurityBundle\Annotation\SecureConfig;
use Phake;

class SecureConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $secure = new SecureConfig(array(
            'domain'  => 'private',
            'auth'    => 'session',
            'forward' => 'AdminController::loginAction',
            'basic'   => 'foo:foopass',
        ));

        $this->assertEquals('private', $secure->domain());
        $this->assertEquals('session', $secure->auth());
        $this->assertEquals('AdminController::loginAction', $secure->forward());
        $this->assertEquals('foo:foopass', $secure->basic());
    }
}
