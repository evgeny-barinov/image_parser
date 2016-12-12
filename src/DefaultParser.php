<?php
/**
 * Date: 11.12.16
 * Time: 16:12
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


use GuzzleHttp\Client;
use Psr\Http\Message\UriInterface;


class DefaultParser
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
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var MetaStorageInterface
     */
    private $metaStorage;

    public function __construct(
        Client $client,
        StorageInterface $storage,
        MetaStorageInterface $metaStorage
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

    /**
     * @param UriInterface $page
     * @return UriInterface[]
     */
    public function parsePage(UriInterface $page)
    {
        $response = $this->client->request('GET', $page);
        $filter = $this->imageFilter;

        $images = $filter($response, $page);
        /**
         * @var ImageInterface $image
         */
        foreach ($images as $image) {
            $this->storage->add($image);
        }

        try {
            $this->storage->save();
            $this->storage->saveMeta($this->metaStorage);
        } catch (StorageException $e) {
            //do something
        } catch (\Exception $e) {
            //do something
        }

        $pagesFilter = $this->pageFilter;
        $pages = $pagesFilter($response, $page);

        return $pages;
    }

    /**
     * @param UriInterface $page
     */
    public function parseSite(UriInterface $page)
    {
        $pages = [$page];

        while (!empty($pages)) {
            $page = array_pop($pages);
            $pages = array_merge($pages, $this->parsePage($page));
        }
    }

}
