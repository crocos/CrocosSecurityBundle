<?php

namespace Crocos\SecurityBundle\Tests\Security;

use Crocos\SecurityBundle\Security\PreviousUrlHandler;
use Phake;

class PreviousUrlHandlerTest extends \PHPUnit_Framework_TestCase
{
    protected $session;
    protected $handler;

    protected function setUp()
    {
        $session = Phake::mock('Symfony\Component\HttpFoundation\Session');

        $handler = new PreviousUrlHandler($session);

        $this->session = $session;
        $this->handler = $handler;
    }

    public function testHasUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn('http://localhost/');

        $this->handler->setup('default');

        $this->assertTrue($this->handler->has());
    }

    public function testHasNotUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn(null);

        $this->handler->setup('default');

        $this->assertFalse($this->handler->has());
    }

    public function testSetUrl()
    {
        $this->handler->setup('default');

        $this->handler->set('http://localhost/previous');

        Phake::verify($this->session)->set('default._previous_url', 'http://localhost/previous');

        $this->assertEquals($this->handler->get(), 'http://localhost/previous');
    }

    public function testGetUrl()
    {
        Phake::when($this->session)->get('default._previous_url')->thenReturn('http://localhost/previous');

        $this->handler->setup('default');

        $this->assertEquals('http://localhost/previous', $this->handler->get());

        Phake::verify($this->session)->remove('default._previous_url');

        $this->assertEquals('http://localhost/previous', $this->handler->get());
    }
}
