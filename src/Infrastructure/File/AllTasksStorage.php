<?php declare(strict_types=1);

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksStorage as AllTasksStorageInterface;

final class AllTasksStorage implements AllTasksStorageInterface
{
    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if(!file_exists($this->filename) && !touch($this->filename)) {
            throw new \Exception(sprintf('Failed to create storage file %s', $this->filename));
        }
    }

    private $filename;

    public function save(array $allTasks): void
    {
        $data = array(
//            'version' => $version,
            'data' => $allTasks,
        );

        if(file_put_contents($this->filename, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception(sprintf('Failed to save projection to "%s".', $this->filename));
        }
    }

    public function load(): array
    {
        $decoded = json_decode(file_get_contents($this->filename), true);

        return $decoded['data'] ?? [];
    }
}
