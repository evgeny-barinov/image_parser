<?php
/**
 * Date: 11.12.16
 * Time: 16:12
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class ImageParser
{
    /**
     * @var callable
     */
    private $imageFilter;

    /**
     * @var callable
     */
    private $pageFilter;

    /**
     * @var ImageStorageInterface
     */
    private $storage;

    /**
     * @var ImageMetaStorageInterface
     */
    private $metaStorage;

    public function __construct(
        Client $client,
        ImageStorageInterface $storage,
        ImageMetaStorageInterface $metaStorage
    )
    {
        $this->client = $client;
        $this->storage = $storage;
        $this->metaStorage = $metaStorage;
    }

    public function addStorageFilter(callable $filter)
    {
        $this->storage->addFilter($filter);
    }

    public function setImageFilter(callable $filter)
    {
        $this->imageFilter = $filter;
    }

    public function setPageFilter(callable $filter)
    {
        $this->pageFilter = $filter;
    }

    public function parsePage(Uri $page)
    {
        $response = $this->client->request('GET', $page);
        $filter = $this->imageFilter;

        $images = $filter($response, $page);
        /**
         * @var ImageInterface $image
         */
        foreach ($images as $image) {
            if ($id = $this->storage->add($image)) {

            }
        }

        $this->storage->save();

        $pagesFilter = $this->pageFilter;
        $pages = $pagesFilter($response, $page);

        return $pages;
    }

    public function parseSite(Uri $page)
    {
        $pages = [$page];

        while (!empty($pages)) {
            $page = array_pop($pages);
            $pages = array_merge($pages, $this->parsePage($page));
        }
    }

}
