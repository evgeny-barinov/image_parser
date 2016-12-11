<?php
/**
 * Date: 11.12.16
 * Time: 16:24
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


interface ImageStorageInterface
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
     * @throws ImageStorageException
     * @return ImageInterface|false
     */
    public function getById($id);

    /**
     * @return null
     */
    public function save();
}
