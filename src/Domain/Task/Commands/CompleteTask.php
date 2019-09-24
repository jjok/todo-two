<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore;
use jjok\TodoTwo\Domain\Task\Query\GetById as GetTaskById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class CompleteTask
{
    public function __construct(EventStore $eventStore, GetTaskById $getTaskById)
    {
        $this->eventStore = $eventStore;
        $this->getTaskById = $getTaskById;
    }

    private $eventStore, $getTaskById;

    /** @throws TaskNotFound */
    public function execute(string $id, string $by) : void
    {
        $task = $this->getTaskById->execute($id);

        $task->complete($by);

        $this->eventStore->push(...$task->releaseEvents());
    }
}
