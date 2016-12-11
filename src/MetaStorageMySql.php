<?php
/**
 * Date: 11.12.16
 * Time: 19:53
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya;


class MetaStorageMySql implements ImageMetaStorageInterface
{
    protected $connection;

    protected $table = 'images';

    public function __construct($dsn, $username, $password)
    {
        $this->connection = new \PDO($dsn, $username, $password);
    }

    public function save(ImageInterface $image)
    {
        $query = "INSERT INTO {$this->table} (name, original_name, content_type) VALUES (?, ?, ?)";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$image->getName(), $image->getOriginalName(), $image->getMime()]);
    }

    public function saveAll($images)
    {
        // TODO: Implement saveAll() method.
    }
}
