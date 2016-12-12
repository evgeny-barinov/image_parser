<?php
/**
 * Date: 11.12.16
 * Time: 19:53
 * @author Evgeniy Barinov <z.barinov@gmail.com>
 */

namespace Barya\ImageParser;


class MetaStorageMySql implements MetaStorageInterface
{
    protected $connection;

    protected $table = 'images';

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(ImageInterface $image)
    {
        $query = "INSERT INTO {$this->table} (name, original_name, uri, content_type) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($query);

        $data = [$image->getName(), $image->getOriginalName(), $image->getUri(), $image->getMime()];
        foreach ($data as $value) {
            if (empty($value)) {
                throw new StorageException('Image is not valid');
            }
        }

        return $stmt->execute($data);
    }

    public function saveAll($images)
    {
        // TODO: Implement saveAll() method.
    }
}
