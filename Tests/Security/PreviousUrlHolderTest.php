<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\PreviousUrlHolder;
use Phake;

class PreviousUrlHolderTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $holder;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session\Session');

        $holder = new PreviousUrlHolder($session);

        $this->session = $session;
        $this->holder = $holder;
    }

    public function testHasUrl()
    {
        Phake::when($this->session)->get('secured._previous_url')->thenReturn('http://localhost/');

        $this->holder->setup('secured');

        $this->assertTrue($this->holder->has());
    }

    public function testHasNotUrl()
    {
        Phake::when($this->session)->get('secured._previous_url')->thenReturn(null);

        $this->holder->setup('secured');

        $this->assertFalse($this->holder->has());
    }

    public function testSetUrl()
    {
        $this->holder->setup('secured');

        $this->holder->set('http://localhost/previous');

        Phake::verify($this->session)->set('secured._previous_url', 'http://localhost/previous');

        $this->assertEquals($this->holder->get(), 'http://localhost/previous');
    }

    public function testGetUrl()
    {
        Phake::when($this->session)->get('secured._previous_url')->thenReturn('http://localhost/previous');

        $this->holder->setup('secured');

        $this->assertEquals('http://localhost/previous', $this->holder->get());

        Phake::verify($this->session)->remove('secured._previous_url');

        $this->assertEquals('http://localhost/previous', $this->holder->get());
    }
}
