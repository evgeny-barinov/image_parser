<?php
/**
 * Date: 11.12.16
 * Time: 16:37
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


interface MetaStorageInterface
{
    /**
     * @param ImageInterface $image
     * @return true|false
     */
    public function save(ImageInterface $image);

    /**
     * @param ImageInterface[] $images
     * @return void
     */
    public function saveAll($images);
}
