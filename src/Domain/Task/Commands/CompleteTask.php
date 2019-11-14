<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore;
use jjok\TodoTwo\Domain\Task\Id as TaskId;
use jjok\TodoTwo\Domain\Task\Query\GetById as GetTaskById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;
use jjok\TodoTwo\Domain\User\Id as UserId;
use jjok\TodoTwo\Domain\User\Query\GetUserById;

final class CompleteTask
{
    public function __construct(EventStore $eventStore, GetTaskById $getTaskById, GetUserById $getUserById)
    {
        $this->eventStore = $eventStore;
        $this->getTaskById = $getTaskById;
        $this->getUserById = $getUserById;
    }

    private $eventStore, $getTaskById, $getUserById;

    /**
     * @throws TaskNotFound
     * @throws \jjok\TodoTwo\Domain\User\NotFound
     */
    public function execute(TaskId $taskId, UserId $userId) : void
    {
        $task = $this->getTaskById->execute($taskId);
        $user = $this->getUserById->execute($userId);

        $task->complete($user->id(), $user->name());

        $this->eventStore->push(...$task->releaseEvents());
    }
}
