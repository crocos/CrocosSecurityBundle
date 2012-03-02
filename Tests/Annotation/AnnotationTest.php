<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Phake;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testDisabledMagicSet()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', array());

        $annot->wozozo = 'unk';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testDisabledMagicGet()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', array());

        $annot->wozozo;
    }
}
