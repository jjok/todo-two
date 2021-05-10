<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore;
use jjok\TodoTwo\Domain\Task\Id as TaskId;
use jjok\TodoTwo\Domain\Task\Query\GetById as GetTaskById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class RenameTask
{
    public function __construct(EventStore $eventStore, GetTaskById $getTaskById)
    {
        $this->eventStore = $eventStore;
        $this->getTaskById = $getTaskById;
    }

    private EventStore $eventStore;
    private GetTaskById $getTaskById;

    /** @throws TaskNotFound */
    public function execute(TaskId $id, string $newName) : void
    {
        $task = $this->getTaskById->execute($id);

        $task->rename($newName);

        $this->eventStore->push(...$task->releaseEvents());
    }
}
