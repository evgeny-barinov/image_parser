<?php

use PHPUnit\Framework\TestCase;
use Barya\ImageParser as parser;

class DefaultImageTest extends TestCase
{

    public function imageProvider() {
        return [
            [
                '117191.jpg',
                'http://cdn.f1ne.ws/im/c/145x108/userfiles/117191.jpg',
                file_get_contents(__DIR__.'/data/117191.jpg')
            ]
        ];
    }

    /**
     * @dataProvider imageProvider()
     */
    public function testImageCreate($name, $uri, $content)
    {
        $image = new parser\DefaultImage($name, $uri, $content);

        $this->assertEquals($name, $image->getOriginalName());
        $this->assertEquals(md5($name).'.jpg', $image->getName());
        $this->assertEquals(strlen($content), $image->getSize());
        $this->assertEquals($content, $image->getContent());
        $this->assertEquals($uri, $image->getUri());
        $this->assertEquals('image/jpeg', $image->getMime());
    }
}
