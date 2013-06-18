<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Security\AnnotationLoader;
use Crocos\SecurityBundle\Security\HttpAuth;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getLoadAnnotationData
     */
    public function testLoadAnnotation($object, $method, $secure, $allow, $domain, $options, $auth, $roleMng, $forward, $basic = null)
    {
        $context = Phake::partialMock('Crocos\SecurityBundle\Security\SecurityContext');

        $previousUrlHolder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $context->setPreviousUrlHolder($previousUrlHolder);

        $httpAuthFacory = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');
        $basicAuth = new HttpAuth\BasicAuth(array('foo' => 'foopass'), $domain);
        Phake::when($httpAuthFacory)->create('basic', 'foo:foopass', $domain)->thenReturn($basicAuth);

        $reflObject = new \ReflectionObject($object);

        $resolver = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver');
        $roleManagerResolver = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerResolver');
        $authLogic = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\ComplexedAuthLogicInterface');
        $roleManager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        Phake::when($resolver)->resolveAuthLogic($auth ?: AnnotationLoader::DEFAULT_AUTH_LOGIC)->thenReturn($authLogic);
        Phake::when($roleManagerResolver)->resolveRoleManager($roleMng ?: AnnotationLoader::DEFAULT_ROLE_MANAGER)->thenReturn($roleManager);

        $loader = new AnnotationLoader(new AnnotationReader(), $resolver, $roleManagerResolver, $httpAuthFacory);
        $loader->load($context, $reflObject, $reflObject->getMethod($method));

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($allow, $context->getAllowedRoles());
        $this->assertEquals($forward, $context->getForwardingController());
        $this->assertEquals($authLogic, $context->getAuthLogic());
        $this->assertEquals($roleManager, $context->getRoleManager());
        $this->assertEquals($options, $context->getOptions());

        if ($basic) {
            $this->assertTrue($context->useHttpAuth());
            $this->assertEquals($basicAuth, $context->getHttpAuth());
        } else {
            $this->assertFalse($context->useHttpAuth());
        }

        Phake::verify($context)->fixDomain();
    }

    public function getLoadAnnotationData()
    {
        $uforward = 'Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction';
        $aforward = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';
        $fforward = 'Crocos\SecurityBundle\Tests\Fixtures\FacebookController::loginAction';

        return array(
            // object, method, secure, allow, domain, options, auth, roleManager, forward [, basic]

            array(new Fixtures\UserController(), 'securedAction', true, array(), 'secured', array(), 'session', 'session', $uforward),
            array(new Fixtures\UserController(), 'publicAction', false, array(), 'secured', array(), 'session', 'session', $uforward),
            array(new Fixtures\UserController(), 'loginAction',  false, array(), 'secured', array(), 'session', 'session', $uforward),

            array(new Fixtures\AdminController(), 'securedAction', true, array('admin'), 'admin', array(), 'session', 'in_memory', $aforward),
            array(new Fixtures\AdminController(), 'publicAction', false, array('admin'), 'admin', array(), 'session', 'in_memory', $aforward),

            array(new Fixtures\SuperAdminController(), 'superAction', true, array('superadmin'), 'admin', array(), 'session', 'in_memory', $aforward),
            array(new Fixtures\SuperAdminController(), 'hyperAction', true, array('hyperadmin'), 'admin', array(), 'session', 'in_memory', $aforward),
            array(new Fixtures\SuperAdminController(), 'hyper2Action', true, array('hyper2admin'), 'admin', array(), 'session', 'in_memory', $aforward),

            array(new Fixtures\FacebookController(), 'securedAction', true, array(), 'facebook', array('group' => array('10000001' => 'ADMIN')), 'facebook', 'session', $fforward),

            array(new Fixtures\BasicSecurityController(), 'securedAction', false, array(), 'secured', array(), null, null, null, 'foo:foopass')
        );
    }
}
