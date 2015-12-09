<?php
namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\SecurityContext;
use Phake;

class SecurityContextTest extends \PHPUnit_Framework_TestCase
{
    protected $context;
    protected $authLogic;
    protected $roleManager;

    protected function setUp()
    {
        $context = new SecurityContext();

        $authLogic = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\ComplexedAuthLogicInterface');
        $context->setAuthLogic($authLogic);

        $roleManager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        $context->setRoleManager($roleManager);

        $this->context = $context;
        $this->authLogic = $authLogic;
        $this->roleManager = $roleManager;
    }

    public function testContextBeforeLoading()
    {
        $context = new SecurityContext();

        $this->assertFalse($context->isSecure());
        $this->assertEmpty($context->getAllowedRoles());
        $this->assertEquals('secured', $context->getDomain());

        $this->assertEmpty($context->getUser());
        $this->assertFalse($context->isAuthenticated());
    }

    public function testSetSecure()
    {
        $this->context->setSecure(true);

        $this->assertTrue($this->context->isSecure());
    }

    public function testSetAllowedRoles()
    {
        $this->context->setAllowedRoles(['FOO', 'BAR']);

        $this->assertEquals(['FOO', 'BAR'], $this->context->getAllowedRoles());
    }

    public function testHasAllowedRoles()
    {
        $manager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');

        $this->context->setRoleManager($manager);
        $this->context->setAllowedRoles(['FOO', 'BAR']);

        Phake::when($manager)->hasRole(['FOO', 'BAR'])->thenReturn(true);

        $this->assertTrue($this->context->hasAllowedRoles());
    }

    public function testSetDomain()
    {
        $this->context->setDomain('private');

        $this->assertEquals('private', $this->context->getDomain());
    }

    public function testLogin()
    {
        $this->context->login('user');

        Phake::verify($this->authLogic)->login('user');
    }

    public function testLogout()
    {
        $this->context->logout();

        Phake::verify($this->roleManager)->clearRoles();
        Phake::verify($this->authLogic)->logout();
    }

    public function testGetUser()
    {
        $this->context->getUser();

        Phake::verify($this->authLogic)->getUser();
    }

    public function testIsAuthenticated()
    {
        $this->context->isAuthenticated();

        Phake::verify($this->authLogic)->isAuthenticated();
    }

    public function testForwardingController()
    {
        $this->context->setForwardingController('SecurityController::loginAction');

        $this->assertEquals('SecurityController::loginAction', $this->context->getForwardingController());
    }

    public function testPreviousUrlHolder()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $this->context->setPreviousUrlHolder($holder);

        $this->assertEquals($holder, $this->context->getPreviousUrlHolder());
    }

    public function testHasPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        Phake::when($holder)->has()->thenReturn(true);

        $this->context->setPreviousUrlHolder($holder);

        $this->assertTrue($this->context->hasPreviousUrl());
    }

    public function testSetPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');

        $this->context->setPreviousUrlHolder($holder);

        $this->context->setPreviousUrl('http://example.com/previous');

        Phake::verify($holder)->set('http://example.com/previous');
    }

    public function testGetPreviousUrl()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        Phake::when($holder)->get()->thenReturn('http://example.com/previous');

        $this->context->setPreviousUrlHolder($holder);

        $this->assertEquals('http://example.com/previous', $this->context->getPreviousUrl());
    }

    public function testUseHttpAuthReturnsFalseByDefault()
    {
        $this->assertFalse($this->context->useHttpAuth());
    }

    public function testUseHttpAuth()
    {
        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $this->context->enableHttpAuth('test', $httpAuth);

        $this->assertTrue($this->context->useHttpAuth());
    }

    public function testGetHttpAuth()
    {
        $httpAuth = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthInterface');
        $this->context->enableHttpAuth('test', $httpAuth);

        $this->assertEquals($httpAuth, $this->context->getHttpAuth('test'));
        $this->assertEquals(['test' => $httpAuth], $this->context->getHttpAuths());
    }

    /**
     * @expectedException LogicException
     */
    public function testLoginThrowLogicExceptionIfNoAuthLogicConfigured()
    {
        $context = new SecurityContext();
        $context->login('user');
    }

    /**
     * @expectedException LogicException
     */
    public function testLogoutThrowLogicExceptionIfNoAuthLogicConfigured()
    {
        $context = new SecurityContext();
        $context->logout();
    }

    public function testIsAuthenticatedReturnsFalseIfNoAuthLogicConfigured()
    {
        $context = new SecurityContext();

        $this->assertFalse($context->isAuthenticated());
    }

    public function testGetUserReturnsNullIfNoAuthLogicConfigured()
    {
        $context = new SecurityContext();

        $this->assertEmpty($context->getUser());
    }

    public function testHasRole()
    {
        $manager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        Phake::when($manager)->hasRole('FOO')->thenReturn(true);

        $this->context->setRoleManager($manager);

        $this->assertEquals(true, $this->context->hasRole('FOO'));
    }

    public function testSetRoles()
    {
        $manager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');

        $this->context->setRoleManager($manager);

        $this->context->setRoles(['FOO', 'BAR']);

        Phake::verify($manager)->setRoles(['FOO', 'BAR']);
    }

    public function testAddRoles()
    {
        $manager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');

        $this->context->setRoleManager($manager);

        $this->context->addRoles(['FOO', 'BAR']);

        Phake::verify($manager)->addRoles(['FOO', 'BAR']);
    }

    public function testGetRoles()
    {
        $manager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        Phake::when($manager)->getRoles()->thenReturn(['FOO', 'BAR']);

        $this->context->setRoleManager($manager);

        $this->assertEquals(['FOO', 'BAR'], $this->context->getRoles());
    }

    public function testFixDomain()
    {
        $holder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $this->context->setPreviousUrlHolder($holder);

        $this->context->setDomain('secured');

        $this->context->fixDomain();

        Phake::verify($this->authLogic)->setDomain('secured');
        Phake::verify($this->roleManager)->setDomain('secured');
        Phake::verify($holder)->setup('secured');
    }
}
