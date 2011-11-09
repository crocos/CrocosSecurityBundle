<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $auth;

    protected function setUp()
    {
        $context = new SecurityContext();

        $auth = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface');
        $context->setAuthLogic($auth);

        $this->context = $context;
        $this->auth = $auth;;
    }

    public function testDelegateToLogic()
    {
        $context = $this->context;

        $context->login('user');
        $context->logout();
        $context->getUser();
        $context->isAuthenticated();

        Phake::verify($this->auth)->login('user');
        Phake::verify($this->auth)->logout();
        Phake::verify($this->auth)->getUser();
        Phake::verify($this->auth)->isAuthenticated();
    }
}
