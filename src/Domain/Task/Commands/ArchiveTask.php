<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore;
use jjok\TodoTwo\Domain\Task\Id as TaskId;
use jjok\TodoTwo\Domain\Task\Query\GetById as GetTaskById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class ArchiveTask
{
    public function __construct(EventStore $eventStore, GetTaskById $getTaskById)
    {
        $this->eventStore = $eventStore;
        $this->getTaskById = $getTaskById;
    }

    private EventStore $eventStore;
    private GetTaskById $getTaskById;

    /** @throws TaskNotFound */
    public function execute(TaskId $taskId) : void
    {
        $task = $this->getTaskById->execute($taskId);

        $task->archive();

        $this->eventStore->push(...$task->releaseEvents());
    }
}
