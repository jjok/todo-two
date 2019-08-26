<?php

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
        //TODO check task doesn't already exist? By Name?

        $task = Task::create2($id, $name, $priority);

        $this->eventStore->push(...$task->releaseEvents());
    }
}
