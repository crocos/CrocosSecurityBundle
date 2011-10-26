<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;

    protected function setUp()
    {
        $this->context = new SecurityContext();
    }

    public function testDefault()
    {
        $context = $this->context;

        $this->assertFalse($context->isSecure());
        $this->assertEquals(array(), $context->getRequiredRoles());
        $this->assertEquals('default', $context->getDomain());
        $this->assertEquals('default', $context->getStrategy());
        $this->assertFalse($context->isAuthenticated());
        $this->assertEmpty($context->getUser());
    }

    public function testAuthenticatedAfterSetUser()
    {
        $context = $this->context;

        $user = new Fixtures\User();
        $context->login($user);

        $this->assertTrue($context->isAuthenticated());
        $this->assertEquals($user, $context->getUser());
    }

    public function testNotAuthenticatedAfterSetEmpty()
    {
        $context = $this->context;

        $user = new Fixtures\User();
        $context->login($user);

        $context->logout();

        $this->assertFalse($context->isAuthenticated());
        $this->assertEmpty($context->getUser());
    }
}
