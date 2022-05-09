<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Query;

use jjok\TodoTwo\Domain\EventStream;
use jjok\TodoTwo\Domain\Task;
use jjok\TodoTwo\Domain\Task\Id as TaskId;

final class GetById
{
    private EventStream $eventStream;

    public function __construct(EventStream $eventStream)
    {
        $this->eventStream = $eventStream;
    }

    /**
     * @throws TaskNotFound
     */
    public function execute(TaskId $id) : Task
    {
        $events = [];
        foreach($this->eventStream->filterByTaskId($id) as $event) {
            $events[] = $event;
        }

        if(count($events) === 0) {
            throw TaskNotFound::fromId($id);
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
