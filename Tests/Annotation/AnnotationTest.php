<?php

namespace Crocos\SecurityBundle\Tests\Exception;

use Phake;

class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    public function testMagicSet()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', []);

        $annot->wozozo = 'unk';

        $this->assertEquals('unk', $annot->wozozo);
        $this->assertEquals('unk', $annot->wozozo());
    }

    public function testMagicGetReturnsNullByDefault()
    {
        $annot = Phake::partialMock('Crocos\SecurityBundle\Annotation\Annotation', []);

        $this->assertNull($annot->wozozo);
        $this->assertNull($annot->wozozo());
    }
}
