<?php

namespace jjok\TodoTwo\Domain\Task\Query;

use jjok\TodoTwo\Domain\EventStream;
use jjok\TodoTwo\Domain\Task;

final class GetById
{
    private $eventStream;

    public function __construct(EventStream $eventStream)
    {
        $this->eventStream = $eventStream;
    }

    /**
     * @throws TaskNotFound
     */
    public function execute(string $id) : Task
    {
        $events = [];
        foreach($this->eventStream->filterByTaskId(Task\Id::fromString($id)) as $event) {
            $events[] = $event;
        }

        if(count($events) === 0) {
            throw TaskNotFound::fromId(Task\Id::fromString($id));
        }

        return Task::fromEvents(...$events);
    }
}

final class TaskNotFound extends \Exception
{
    public static function fromId(Task\Id $taskId) : self
    {
        return new self(sprintf('No task exists with ID "%s".', $taskId->toString()));
    }
}
