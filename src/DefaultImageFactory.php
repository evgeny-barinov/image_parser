<?php
/**
 * Date: 11.12.16
 * Time: 23:22
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

class DefaultImageFactory
{
    public function create($path)
    {
        if (empty($path)) {
            return false;
        }

        $uri = new Uri($path);
        $client = new Client();

        if ($content = (string) $client->request('GET', $uri)->getBody()) {
            $path = explode('/', $uri->getPath());
            return new DefaultImage(end($path), $content);
        }

        return false;
    }
}
