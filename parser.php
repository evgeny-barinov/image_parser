<?php
/**
 * Date: 11.12.16
 * Time: 16:06
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

require __DIR__ . '/vendor/autoload.php';

$opts = getopt('s:');

if (empty($opts['s'])) {
    die(1);
}


$p = new \Barya\ImageParser(
    new \GuzzleHttp\Client(),
    new \Barya\StorageFileSystem(__DIR__ . DIRECTORY_SEPARATOR . 'images'),
    new \Barya\MetaStorageMySql("mysql:dbname=parsertest;host=127.0.0.1", "root", "")
);

$p->addStorageFilter(function(\Barya\ImageInterface $image) {
    return $image->getMime() == 'image/jpeg';
});
$p->addStorageFilter(function(\Barya\ImageInterface $image) {
    return $image->getContent() < 10 * 1024 * 1024;
});

$p->setImageFilter(function (\GuzzleHttp\Psr7\Response $response, \GuzzleHttp\Psr7\Uri $baseUri) {
    $doc = new DOMDocument();

    $images = [];
    if ($doc->loadHTML($response->getBody())) {
        $imgs = $doc->getElementsByTagName('img');

        $defaultImageFactory = new \Barya\DefaultImageFactory();
        /**
         * @var DOMElement $img
         */
        foreach ($imgs as $img) {
            $uri = new \GuzzleHttp\Psr7\Uri($img->getAttribute('src'));
            $src = $uri->getHost() ? $uri : $baseUri . $uri;
            if ($image = $defaultImageFactory->create($src)) {
                $images[] = $image;
            }
        }
    }
    return $images;
});

$p->setPageFilter(function (\GuzzleHttp\Psr7\Response $response, \GuzzleHttp\Psr7\Uri $baseUri) {
    return [];
});

$p->parsePage(new \GuzzleHttp\Psr7\Uri($opts['s']));