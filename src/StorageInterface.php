<?php
/**
 * Date: 11.12.16
 * Time: 16:24
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


interface StorageInterface
{
    /**
     * @param ImageInterface $image
     * @return string ImageId|false
     */
    public function add(ImageInterface $image);

    /**
     * @param callable $filter
     * @return mixed
     */
    public function addFilter(callable $filter);

    /**
     * @return ImageInterface[]
     */
    public function getAll();

    /**
     * @param $id
     * @return ImageInterface|false
     */
    public function getById($id);

    /**
     * @return null
     */
    public function save();

    /**
     * @param MetaStorageInterface $metastorage
     * @return mixed
     */
    public function saveMeta(MetaStorageInterface $metastorage);
}
