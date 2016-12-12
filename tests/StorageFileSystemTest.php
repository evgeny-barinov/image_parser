<?php

use PHPUnit\Framework\TestCase;
use Barya\ImageParser as parser;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStream;

class StorageFileSystemTest extends TestCase
{
    /**
     * @var parser\ImageInterface
     */
    protected static $image;

    public static function setUpBeforeClass()
    {
        self::$image = new parser\DefaultImage(
            '117191.jpg',
            'http://cdn.f1ne.ws/im/c/145x108/userfiles/117191.jpg',
            file_get_contents(__DIR__.'/data/117191.jpg')
        );
    }

    public function testAddImage()
    {
        $repo = new parser\StorageFileSystem();
        $id = $repo->add(self::$image);

        $this->assertTrue(is_string($id));
        $this->assertInstanceOf(parser\ImageInterface::class, $repo->getById($id));
        $this->assertEquals(1, sizeof($repo->getAll()));
        $this->assertContains($id, array_keys($repo->getAll()));
        $this->assertContainsOnlyInstancesOf(parser\ImageInterface::class, $repo->getAll());
    }

    public function testAddWithPositiveFilter()
    {
        $repo = new parser\StorageFileSystem();
        $repo->addFilter(function(parser\ImageInterface $image) {return true;});
        $id = $repo->add(self::$image);

        $this->assertTrue(is_string($id));
        $this->assertInstanceOf(parser\ImageInterface::class, $repo->getById($id));
        $this->assertEquals(1, sizeof($repo->getAll()));
        $this->assertContains($id, array_keys($repo->getAll()));
        $this->assertContainsOnlyInstancesOf(parser\ImageInterface::class, $repo->getAll());
    }

    public function testAddWithNegativeFilter()
    {
        $repo = new parser\StorageFileSystem();
        $repo->addFilter(function(parser\ImageInterface $image) {return false;});
        $id = $repo->add(self::$image);

        $this->assertFalse($id);
        $this->assertEquals(0, sizeof($repo->getAll()));
    }

    public function testSave()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root_dir'));
        $repo = new parser\StorageFileSystem(vfsStream::url('root_dir/test'));
        $repo->add(self::$image);
        $repo->save();
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('test/' . self::$image->getName()));
    }

    /**
     * @expectedException \Barya\ImageParser\StorageException
     */
    public function testSaveToNonWritableDir()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root_dir'));
        vfsStreamWrapper::getRoot()->addChild(new vfsStreamDirectory('test'));
        vfsStreamWrapper::getRoot()->getChild('test')->chmod(0000);
        $repo = new parser\StorageFileSystem(vfsStream::url('root_dir/test'));
        $repo->add(self::$image);
        $repo->save();
    }


    public function testSaveMeta()
    {
        $metaStorage = $this->createMock(parser\MetaStorageInterface::class);
        $this->assertInstanceOf(parser\MetaStorageInterface::class, $metaStorage);
        $metaStorage->expects($this->once())->method('save');

        $repo = new parser\StorageFileSystem();
        $repo->add(self::$image);

        $repo->saveMeta($metaStorage);
    }

    /**
     * @expectedException \Barya\ImageParser\StorageException
     */
    public function testSaveMetaThrowsException()
    {
        $metaStorage = $this->createMock(parser\MetaStorageInterface::class);
        $this->assertInstanceOf(parser\MetaStorageInterface::class, $metaStorage);
        $metaStorage->method('save')->willThrowException(new parser\StorageException());

        $repo = new parser\StorageFileSystem();
        $repo->add(self::$image);

        $repo->saveMeta($metaStorage);
    }
}
