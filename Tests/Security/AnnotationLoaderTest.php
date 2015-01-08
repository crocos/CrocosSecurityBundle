<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Crocos\SecurityBundle\Security\AnnotationLoader;
use Crocos\SecurityBundle\Security\HttpAuth;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        // I don't know why; Load Secure class before run test because annotation parser failed to load class...
        class_exists('Crocos\SecurityBundle\Annotation\Secure', true);
    }

    /**
     * @dataProvider getLoadAnnotationData
     */
    public function testLoadAnnotation($object, $method, $secure, $allow, $domain, $options, $auth, $roleMng, $https, $forward, $basic = null)
    {
        $context = new SecurityContext();

        $previousUrlHolder = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHolder');
        $context->setPreviousUrlHolder($previousUrlHolder);

        $basicAuthFacory = Phake::partialMock('Crocos\SecurityBundle\Security\HttpAuth\BasicAuthFactory');
        $basicAuth = new HttpAuth\BasicAuth(['foo' => 'foopass'], $domain);
        Phake::when($basicAuthFacory)->create('foo:foopass', $domain)->thenReturn($basicAuth);

        $parameterResolver = Phake::mock('Crocos\SecurityBundle\Security\ParameterResolverInterface');
        Phake::when($parameterResolver)->resolveValue(Phake::capture($param))->thenGetReturnByLambda(function () use (&$param) {
            $param = str_replace([
                '%auth.https%',
                '%auth.basic%',
            ], [
                true,
                'foo:foopass',
            ], $param);

            return $param;
        });

        $reflObject = new \ReflectionObject($object);

        $resolver = Phake::mock('Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver');
        $authLogic = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\ComplexedAuthLogicInterface');
        Phake::when($resolver)->resolveAuthLogic($auth ?: AnnotationLoader::DEFAULT_AUTH_LOGIC)->thenReturn($authLogic);

        $roleManagerResolver = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerResolver');
        $roleManager = Phake::mock('Crocos\SecurityBundle\Security\Role\RoleManagerInterface');
        Phake::when($roleManagerResolver)->resolveRoleManager($roleMng ?: AnnotationLoader::DEFAULT_ROLE_MANAGER)->thenReturn($roleManager);

        $loader = new AnnotationLoader(new AnnotationReader(), $resolver, $roleManagerResolver);
        $loader->addHttpAuthFactory($basicAuthFacory);
        $loader->setParameterResolver($parameterResolver);

        $loader->load($context, $reflObject, $reflObject->getMethod($method));

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($allow, $context->getAllowedRoles());
        $this->assertEquals($forward, $context->getForwardingController());
        $this->assertEquals($authLogic, $context->getAuthLogic());
        $this->assertEquals($roleManager, $context->getRoleManager());
        $this->assertEquals($options, $context->getOptions());
        $this->assertEquals($https, $context->getHttpsRequired());

        if ($basic) {
            $this->assertTrue($context->useHttpAuth());
            $this->assertEquals($basicAuth, $context->getHttpAuth('basic'));
        } else {
            $this->assertFalse($context->useHttpAuth());
        }

        $this->assertTrue($context->isDomainFixed());
    }

    public function getLoadAnnotationData()
    {
        $uforward = 'Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction';
        $aforward = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';

        return [
            // object, method, secure, allow, domain, options, auth, roleManager, https, forward [, basic]

            [new Fixtures\UserController(), 'securedAction', true, [], 'secured', [], 'session', 'session', null, $uforward],
            [new Fixtures\UserController(), 'publicAction', false, [], 'secured', [], 'session', 'session', null, $uforward],
            [new Fixtures\UserController(), 'loginAction',  false, [], 'secured', [], 'session', 'session', true, $uforward],

            // admin
            [new Fixtures\AdminController(), 'securedAction', true, ['admin'], 'admin', [], 'session', 'in_memory', true, $aforward],
            [new Fixtures\AdminController(), 'publicAction', false, ['admin'], 'admin', [], 'session', 'in_memory', false, $aforward],
            [new Fixtures\AdminController(), 'securedAction', true, ['admin'], 'admin', [], 'session', 'in_memory', true, $aforward],

            // super admin
            [new Fixtures\SuperAdminController(), 'superAction', true, ['superadmin'], 'admin', [], 'session', 'in_memory', true, $aforward],
            [new Fixtures\SuperAdminController(), 'hyperAction', true, ['hyperadmin'], 'admin', [], 'session', 'in_memory', true, $aforward],
            [new Fixtures\SuperAdminController(), 'hyper2Action', true, ['hyper2admin'], 'admin', [], 'session', 'in_memory', true, $aforward],

            // enable basic
            [new Fixtures\BasicSecurityController(), 'securedAction', false, [], 'secured', [], null, null, true, null, 'foo:foopass'],
        ];
    }
}
