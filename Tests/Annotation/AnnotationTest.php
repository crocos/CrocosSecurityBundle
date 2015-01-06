<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Crocos\SecurityBundle\Annotation\Secure;
use Phake;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException BadMethodCallException
     */
    public function testDisabledMagicSet()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', []);

        $annot->wozozo = 'unk';
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testDisabledMagicGet()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', []);

        $annot->wozozo;
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testCall()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', []);

        $annot->wozozo();
    }

    public function testExtendAttrsSetsDefault()
    {
        Secure::extendAttrs(['ext' => false]);

        $annot = new Secure([]);
        $this->assertEquals(false, $annot->ext());
    }

    public function testExtendAttrs()
    {
        Secure::extendAttrs(['ext' => false]);

        $annot = new Secure(['ext' => true]);
        $this->assertEquals(true, $annot->ext());
    }
}
