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
    public function testLoadAnnotation($object, $method, $secure, $roles, $domain, $auth, $forward, $basic = null)
    {
        $context = new SecurityContext();

        $previousUrlHolder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $context->setPreviousUrlHolder($previousUrlHolder);

        $httpAuthFacory = Phake::mock('Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactory');
        $basicAuth = new HttpAuth\BasicAuth(array('foo' => 'foopass'), $domain);
        Phake::when($httpAuthFacory)->create('basic', 'foo:foopass', $domain)->thenReturn($basicAuth);

        $reflObject = new \ReflectionObject($object);

        $resolver = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver');
        $authLogic = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicInterface');
        Phake::when($resolver)->resolveAuthLogic($auth ?: AnnotationLoader::DEFAULT_AUTH_LOGIC)->thenReturn($authLogic);

        $loader = new AnnotationLoader(new AnnotationReader(), $resolver, $httpAuthFacory);
        $loader->load($context, $reflObject, $reflObject->getMethod($method));

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($roles, $context->getRequiredRoles());
        $this->assertEquals($forward, $context->getForwardingController());
        $this->assertEquals($authLogic, $context->getAuthLogic());

        if ($basic) {
            $this->assertTrue($context->useHttpAuth());
            $this->assertEquals($basicAuth, $context->getHttpAuth());
        } else {
            $this->assertFalse($context->useHttpAuth());
        }

        Phake::verify($authLogic)->setDomain($domain);
        Phake::verify($previousUrlHolder)->setup($domain);
    }

    public function getLoadAnnotationData()
    {
        $uforward = 'Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction';
        $aforward = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';

        return array(
            // object, method, secure, roles, domain, auth, forward

            array(new Fixtures\UserController(), 'securedAction', true, array(), 'default', 'session', $uforward),
            array(new Fixtures\UserController(), 'publicAction', false, array(), 'default', 'session', $uforward),
            array(new Fixtures\UserController(), 'loginAction', false, array(), 'default', 'session', $uforward),

            array(new Fixtures\AdminController(), 'securedAction', true, array(), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'publicAction', false, array(), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'adminAction', true, array('admin'), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'loginAction', true, array(), 'admin', 'session', $aforward),

            array(new Fixtures\BasicSecurityController(), 'securedAction', false, array(), 'admin', null, null, 'foo:foopass')
        );
    }
}
