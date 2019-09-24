<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore;
use jjok\TodoTwo\Domain\Task;

final class CreateTask
{
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    private $eventStore;

    public function execute(string $id, string $name, int $priority) : void
    {
        //TODO check ID is unique
        //TODO check name is unique

        $task = Task::create($id, $name, $priority);

        $this->eventStore->push(...$task->releaseEvents());
    }
}
