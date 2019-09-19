<?php

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksStorage as AllTasksStorageInterface;

final class TempAllTasksStorage implements AllTasksStorageInterface
{
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        $this->storage = new AllTasksStorage($filename);
    }

    private $filename, $storage;

    public function save(array $allTasks): void
    {
        $this->storage->save($allTasks);
    }

    public function load(): array
    {
        return $this->storage->load();
    }

    public function __destruct()
    {
        if(file_exists($this->filename)) {
            unlink($this->filename);
        }
    }
}
