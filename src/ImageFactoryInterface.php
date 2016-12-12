<?php
/**
 * Date: 12.12.16
 * Time: 11:20
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


interface ImageFactoryInterface
{
    /**
     * @param string $uri abs url
     * @return ImageInterface|false
     */
    public function create($uri);
}
