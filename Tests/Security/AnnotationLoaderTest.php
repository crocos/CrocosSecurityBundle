<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Doctrine\Common\Annotations\AnnotationReader;
use Crocos\SecurityBundle\Annotation\Secure;
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

        $reflObject = new \ReflectionObject($object);

        $loader = new AnnotationLoader(new AnnotationReader());
        $loader->load($context, $reflObject, $reflObject->getMethod($method));

        $this->assertEquals($secure, $context->isSecure());
        $this->assertEquals($roles, $context->getRequiredRoles());
        $this->assertEquals($domain, $context->getDomain());
        $this->assertEquals($strategy, $context->getStrategy());
        $this->assertEquals($forward, $context->getForwardingController());
    }

    public function getLoadAnnotationData()
    {
        $uforward = 'Crocos\SecurityBundle\Tests\Fixtures\UserController::loginAction';
        $aforward = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController::loginAction';

        return array(
            // object, method, secure, roles, domain, strategy, forward

            array(new Fixtures\UserController(), 'securedAction', true, array(), 'default', 'default', $uforward),
            array(new Fixtures\UserController(), 'publicAction', false, array(), 'default', 'default', $uforward),
            array(new Fixtures\UserController(), 'loginAction', false, array(), 'default', 'default', $uforward),

            array(new Fixtures\AdminController(), 'securedAction', true, array(), 'admin', 'default', $aforward),
            array(new Fixtures\AdminController(), 'publicAction', false, array(), 'admin', 'default', $aforward),
            array(new Fixtures\AdminController(), 'adminAction', true, array('admin'), 'admin', 'default', $aforward),
            array(new Fixtures\AdminController(), 'loginAction', true, array(), 'admin', 'default', $aforward),
        );
    }
}
