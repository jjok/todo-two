<?php

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksStorage as AllTasksStorageInterface;

final class AllTasksStorage implements AllTasksStorageInterface
{
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    private $filename;

    public function save(array $allTasks): void
    {
        if(file_put_contents($this->filename, json_encode($allTasks, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception(sprintf('Failed to save projection to "%s".', $this->filename));
        }
    }

    public function load(): array
    {
        return json_decode(file_get_contents($this->filename), true) ?? [];
    }
}
