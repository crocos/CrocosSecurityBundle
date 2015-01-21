<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\Authorizer;
use Phake;

class AuthorizerTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $authorizer;

    protected function setUp()
    {
        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');

        $authorizer = new Authorizer($loader, $matcher);

        $this->context = $context;
        $this->authorizer = $authorizer;
    }

    public function testAuthorizeDoesNotThrowAuthExceptionIfHasAllowedRoles()
    {
        Phake::when($this->context)->getAllowedRoles()->thenReturn(['FOO']);
        Phake::when($this->context)->hasRole(['FOO'])->thenReturn(true);

        $this->authorizer->authorize($this->context);
    }

    /**
     * @expectedException Crocos\SecurityBundle\Exception\AuthException
     */
    public function testAuthorizeThrowsAuthExceptionIfHasNotAllowedRoles()
    {
        Phake::when($this->context)->getAllowedRoles()->thenReturn(['FOO']);
        Phake::when($this->context)->hasRole(['FOO'])->thenReturn(false);

        $this->authorizer->authorize($this->context);
    }
}
