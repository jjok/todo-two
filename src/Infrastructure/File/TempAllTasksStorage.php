<?php declare(strict_types=1);

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksStorage as AllTasksStorageInterface;

final class TempAllTasksStorage implements AllTasksStorageInterface
{
    public function __construct()
    {
        $this->filename = tempnam('', 'all-tasks-');
        $this->storage = new AllTasksStorage($this->filename);
    }

    private $filename, $storage;

    public function save(array $allTasks/*, int $version*/): void
    {
        $this->storage->save($allTasks/*, $version*/);
    }

    public function load(): array
    {
        return $this->storage->load();
    }

    public function version(): int
    {
        return $this->storage->version();
    }

    public function __destruct()
    {
        unlink($this->filename);
    }
}
