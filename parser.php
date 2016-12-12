<?php
/**
 * Date: 11.12.16
 * Time: 16:06
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

use \Psr\Http\Message\ResponseInterface;
use \Psr\Http\Message\UriInterface;
use \Barya\ImageParser as parser;
use \GuzzleHttp\Psr7\Uri;

require __DIR__ . '/vendor/autoload.php';

$opts = getopt('s:');

if (empty($opts['s'])) {
    die(1);
}

$p = new parser\DefaultParser(
    new \GuzzleHttp\Client(),
    new parser\StorageFileSystem(__DIR__ . DIRECTORY_SEPARATOR . 'images'),
    new parser\MetaStorageMySql(
        new \PDO("mysql:dbname=parsertest;host=127.0.0.1", "root", "")
    )
);

$p->addStorageFilter(function(parser\ImageInterface $image) {
    return in_array($image->getMime(), ['image/jpeg', 'image/png']);
});

$p->addStorageFilter(function(parser\ImageInterface $image) {
    return $image->getSize() > 30 * 1024;
});

$p->setImageFilter(function (ResponseInterface $response, UriInterface $baseUri) {
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();

    $images = [];
    if ($doc->loadHTML($response->getBody())) {
        $imgs = $doc->getElementsByTagName('img');

        $defaultImageFactory = new parser\DefaultImageFactory();
        /**
         * @var DOMElement $img
         */
        foreach ($imgs as $img) {
            $uri = new Uri($img->getAttribute('src'));
            $src = $uri->getHost() ? $uri : $baseUri . $uri;
            if ($image = $defaultImageFactory->create($src)) {
                $images[] = $image;
            }
        }
    }
    return $images;
});

$p->setPageFilter(
    function (ResponseInterface $response, UriInterface $baseUri) {
        static $calls = 0;
        if ($calls > 1) {
            return [];
        }
        if ($calls == 0) {
            $calls++;
            return [new Uri('https://lenta.ru/rubrics/world/')];
        }
        if ($calls == 1) {
            $calls++;
            return [new Uri('https://lenta.ru/rubrics/russia/')];
        }
        return [];
    }
);

$p->parseSite(new Uri($opts['s']));
