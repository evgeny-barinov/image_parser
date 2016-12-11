<?php
/**
 * Date: 11.12.16
 * Time: 19:19
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


class StorageFileSystem implements ImageStorageInterface
{
    /**
     * @var callable[]
     */
    protected $filters = [];

    /**
     * @var ImageInterface[]
     */
    protected $images = [];

    protected $dir;

    public function __construct($dir = null)
    {
        $this->dir = is_null($dir) ?
            __DIR__ . DIRECTORY_SEPARATOR . 'images_' . date('d_m_Y') :
            (string) $dir;
    }

    public function add(ImageInterface $image)
    {
        foreach ($this->filters as $filter) {
            if (!$filter($image)) {
                return false;
            }
        }

        $id = md5($image->getOriginalName());
        $this->images[$id] = $image;

        return $id;
    }

    public function getAll()
    {
        return $this->images;
    }

    public function getById($id)
    {
        return empty($this->images[$id]) ? false : $this->images[$id];
    }

    public function save()
    {
        if (!@is_dir($this->dir)) {
            @mkdir($this->dir);
        }

        if (!@is_writable($this->dir)) {
            throw new ImageStorageException('Directory is not writable');
        }

        foreach ($this->images as $image) {
            @file_put_contents($this->dir . DIRECTORY_SEPARATOR . $image->getName(), $image->getContent());
        }
    }

    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }
}
