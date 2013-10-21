<?php

namespace Crocos\SecurityBundle\Tests\Security\AuthLogic;

use Crocos\SecurityBundle\Security\AuthLogic\SessionEntityAuth;
use Phake;

class SessionEntityAuthTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $auth;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');

        $managerRegistry = Phake::mock('Symfony\Bridge\Doctrine\RegistryInterface');

        $auth = new SessionEntityAuth($session, $managerRegistry);
        $auth->setDomain('secured');

        $this->session = $session;
        $this->managerRegistry = $managerRegistry;
        $this->auth = $auth;
    }

    public function testLogin()
    {
        $user = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\User');
        Phake::when($user)->getId()->thenReturn(1);

        $this->auth->login($user);

        Phake::verify($this->session)->set('secured._user', array(
            'v'     => SessionEntityAuth::FORMAT_VERSION,
            'class' => get_class($user),
            'id'    => 1,
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLoginRequiredToImplementGetId()
    {
        $user = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\UnsupportedUser');

        $this->auth->login($user);
    }

    public function testGetUser()
    {
        $user = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\User');
        Phake::when($user)->getId()->thenReturn(1);

        Phake::when($this->session)->get('secured._user', null)->thenReturn(array(
            'v'     => SessionEntityAuth::FORMAT_VERSION,
            'class' => get_class($user),
            'id'    => 1,
        ));

        $repository = $this->setupRepository($user);
        Phake::when($repository)->find(1)->thenReturn($user);

        $this->assertEquals($user, $this->auth->getUser());
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetUserThrowsUnexpectedValueExceptionIfUserNotFound()
    {
        $user = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\User');
        Phake::when($user)->getId()->thenReturn(1);

        Phake::when($this->session)->get('secured._user', null)->thenReturn(array(
            'v'     => SessionEntityAuth::FORMAT_VERSION,
            'class' => get_class($user),
            'id'    => 1,
        ));

        $repository = $this->setupRepository($user);
        Phake::when($repository)->find(1)->thenReturn(null);

        $this->auth->getUser();
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testGetUserThrowsUnexpectedValueExceptionIfUserIsNotEnabled()
    {
        $user = Phake::mock('Crocos\SecurityBundle\Tests\Fixtures\AdvancedUser');
        Phake::when($user)->getId()->thenReturn(1);
        Phake::when($user)->isEnabled()->thenReturn(false);

        Phake::when($this->session)->get('secured._user', null)->thenReturn(array(
            'v'     => SessionEntityAuth::FORMAT_VERSION,
            'class' => get_class($user),
            'id'    => 1,
        ));

        $repository = $this->setupRepository($user);
        Phake::when($repository)->find(1)->thenReturn($user);

        $this->assertEquals($user, $this->auth->getUser());
    }

    protected function setupRepository($user)
    {
        $class = get_class($user);

        $manager = Phake::mock('Doctrine\ORM\EntityManager');
        $repository = Phake::mock('Doctrine\ORM\EntityRepository');

        Phake::when($this->managerRegistry)->getEntityManagerForClass($class)->thenReturn($manager);
        Phake::when($manager)->getRepository($class)->thenReturn($repository);

        return $repository;
    }
}
