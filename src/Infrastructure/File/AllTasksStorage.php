<?php

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksStorage as AllTasksStorageInterface;

final class AllTasksStorage implements AllTasksStorageInterface
{
    public static function temp() : self
    {
        return new self(new \SplTempFileObject());
    }

    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    private $file;

    public function save(array $allTasks): void
    {
        $this->file->rewind();
        $this->file->fwrite(json_encode($allTasks));
    }

    public function load(): array
    {
        $this->file->rewind();

        return json_decode($this->file->current(), true) ?? [];
    }
}
