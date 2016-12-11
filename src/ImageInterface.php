<?php
/**
 * Date: 11.12.16
 * Time: 16:02
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;

/**
 * Interface ImageInterface
 * @package Barya
 */
interface ImageInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getOriginalName();

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return string
     */
    public function getMime();
}
