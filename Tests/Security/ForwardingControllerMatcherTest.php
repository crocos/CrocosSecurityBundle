<?php
namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\ForwardingControllerMatcher;
use Phake;

class ForwardingControllerMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testMatchFQCN()
    {
        $parser = Phake::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser');

        $matcher = new ForwardingControllerMatcher($parser);

        $class = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController';
        $method = 'securedAction';
        $reflClass = new \ReflectionClass($class);
        $reflMethod = $reflClass->getMethod($method);

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($context)->getForwardingController()->thenReturn("{$class}::{$method}");

        $this->assertTrue($matcher->isForwardingController($context, $reflClass, $reflMethod));
    }

    public function testMatchAbbrFormat()
    {
        $parser = Phake::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser');

        $matcher = new ForwardingControllerMatcher($parser);

        $class = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController';
        $method = 'securedAction';
        $reflClass = new \ReflectionClass($class);
        $reflMethod = $reflClass->getMethod($method);

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($context)->getForwardingController()->thenReturn('CrocosSecurityBundle:Admin:secure');

        Phake::when($parser)->parse('CrocosSecurityBundle:Admin:secure')->thenReturn("{$class}::{$method}");

        $this->assertTrue($matcher->isForwardingController($context, $reflClass, $reflMethod));
    }

    public function testNonmatch()
    {
        $parser = Phake::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser');

        $matcher = new ForwardingControllerMatcher($parser);

        $class = 'Crocos\SecurityBundle\Tests\Fixtures\AdminController';
        $method = 'securedAction';
        $reflClass = new \ReflectionClass($class);
        $reflMethod = $reflClass->getMethod($method);

        $context = Phake::mock('Crocos\SecurityBundle\Security\SecurityContext');
        Phake::when($context)->getForwardingController()->thenReturn("{$class}::loginAction");

        $this->assertFalse($matcher->isForwardingController($context, $reflClass, $reflMethod));
    }
}
