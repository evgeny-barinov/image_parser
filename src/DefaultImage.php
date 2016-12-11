<?php
/**
 * Date: 11.12.16
 * Time: 16:39
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


class DefaultImage implements ImageInterface
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $originalName;

    /**
     * @var string
     */
    protected $mime;

    /**
     * DefaultImage constructor.
     * @param string $name
     * @param string $content
     */
    public function __construct($name, $content)
    {
        $this->originalName = $name;
        $this->name = $name;
        $this->content = $content;
        $this->setMime();
    }

    public function getOriginalName()
    {
        return $this->originalName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMime()
    {
        return $this->mime;
    }

    protected function setMime()
    {
        $this->mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($this->content);
    }
}
