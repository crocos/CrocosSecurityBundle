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
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session');

        $holder = new PreviousUrlHolder($session);

        $this->session = $session;
        $this->holder = $holder;
    }

    public function testHasUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn('http://localhost/');

        $this->holder->setup('default');

        $this->assertTrue($this->holder->has());
    }

    public function testHasNotUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn(null);

        $this->holder->setup('default');

        $this->assertFalse($this->holder->has());
    }

    public function testSetUrl()
    {
        $this->holder->setup('default');

        $this->holder->set('http://localhost/previous');

        Phake::verify($this->session)->set('default._previous_url', 'http://localhost/previous');

        $this->assertEquals($this->holder->get(), 'http://localhost/previous');
    }

    public function testGetUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn('http://localhost/previous');

        $this->holder->setup('default');

        $this->assertEquals('http://localhost/previous', $this->holder->get());

        Phake::verify($this->session)->remove('default._previous_url');

        $this->assertEquals('http://localhost/previous', $this->holder->get());
    }
}
