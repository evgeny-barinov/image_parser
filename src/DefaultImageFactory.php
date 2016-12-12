<?php
/**
 * Date: 11.12.16
 * Time: 23:22
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class DefaultImageFactory implements ImageFactoryInterface
{
    public function create($uri)
    {
        if (empty($uri)) {
            return false;
        }

        if ($content = (string) (new Client())->request('GET', $uri)->getBody()) {
            $path_parts = explode('/', (new Uri($uri))->getPath());
            return new DefaultImage(end($path_parts), $uri, $content);
        }

        return false;
    }
}
