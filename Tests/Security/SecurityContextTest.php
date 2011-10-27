<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $strategy;

    protected function setUp()
    {
        $context = new SecurityContext();

        $strategy = Phake::mock('Crocos\SecurityBundle\Security\AuthStrategy\AuthStrategyInterface');
        $context->setStrategy($strategy);

        $this->context = $context;
        $this->strategy = $strategy;;
    }

    public function testDelegateToStrategy()
    {
        $context = $this->context;

        $context->login('user');
        $context->logout();
        $context->getUser();
        $context->isAuthenticated();

        Phake::verify($this->strategy)->login('user');
        Phake::verify($this->strategy)->logout();
        Phake::verify($this->strategy)->getUser();
        Phake::verify($this->strategy)->isAuthenticated();
    }
}
