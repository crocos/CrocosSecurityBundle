<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Crocos\SecurityBundle\Security\SecurityContext;
use Crocos\SecurityBundle\Security\AnnotationLoader;
use Crocos\SecurityBundle\Tests\Fixtures;
use Phake;

class AnnotationLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getLoadAnnotationData
     */
    public function testLoadAnnotation($object, $method, $secure, $roles, $domain, $strategy, $forward)
    {
        $context = new SecurityContext();

        $previousUrlHandler = Phake::mock('Crocos\SecurityBundle\Security\PreviousUrlHandler');
        $context->setPreviousUrlHandler($previousUrlHandler);

        $reflObject = new \ReflectionObject($object);

        $resolver = Phake::mock('Crocos\SecurityBundle\Security\AuthStrategy\AuthStrategyResolver');
        $authStrategy = Phake::mock('Crocos\SecurityBundle\Security\AuthStrategy\AuthStrategyInterface');
        Phake::when($resolver)->resolveAuthStrategy($strategy)->thenReturn($authStrategy);

        $loader = new AnnotationLoader(new AnnotationReader(), $resolver);
        $loader->load($context, $reflObject, $reflObject->getMethod($method));

        Phake::verify($resolver)->resolveAuthStrategy($strategy);

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($roles, $context->getRequiredRoles());
        $this->assertEquals($forward, $context->getForwardingController());
        $this->assertEquals($authStrategy, $context->getStrategy());

        Phake::verify($authStrategy)->setDomain($domain);
        Phake::verify($previousUrlHandler)->setup($domain);
    }

    public function getLoadAnnotationData()
    {
        $uforward = 'Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction';
        $aforward = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';

        return array(
            // object, method, secure, roles, domain, strategy, forward

            array(new Fixtures\UserController(), 'securedAction', true, array(), 'default', 'session', $uforward),
            array(new Fixtures\UserController(), 'publicAction', false, array(), 'default', 'session', $uforward),
            array(new Fixtures\UserController(), 'loginAction', false, array(), 'default', 'session', $uforward),

            array(new Fixtures\AdminController(), 'securedAction', true, array(), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'publicAction', false, array(), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'adminAction', true, array('admin'), 'admin', 'session', $aforward),
            array(new Fixtures\AdminController(), 'loginAction', true, array(), 'admin', 'session', $aforward),
        );
    }
}
