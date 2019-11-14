<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Id as TaskId;
use jjok\TodoTwo\Domain\Task\Query\GetById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;
use jjok\TodoTwo\Domain\User;
use jjok\TodoTwo\Domain\User\Id as UserId;
use jjok\TodoTwo\Domain\User\NotFound as UserNotFound;
use jjok\TodoTwo\Infrastructure\InMemory\GetUserById;

final class CompleteTaskTest extends CommandTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->getUserById = new GetUserById(
            new User(UserId::fromString('887ca7d3-27e3-4964-8378-0f3d0d4aa6d3'), 'Jonathan'),
            new User(UserId::fromString('1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6'), 'Someone Else')
        );
    }

    private $getUserById;

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "887ca7d3-27e3-4964-8378-0f3d0d4aa6d3", "Jonathan"    ]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6", "Someone Else"]
     */
    public function a_task_can_be_completed_by_a_registered_user(string $taskId, string $userId, string $by) : void
    {
        $this->givenTaskAlreadyExists($taskId, 'The name of the task', 50);

        $completeTask = new CompleteTask($this->eventStore, new GetById($this->eventStream), $this->getUserById);

        $completeTask->execute(TaskId::fromString($taskId), UserId::fromString($userId));

        $this->assertTaskWasRecentlyCompleted($taskId, $by);
    }

    private function assertTaskWasRecentlyCompleted(string $id, string $by) : void
    {
        $task = $this->getStoredTask($id);

        $this->assertSame($by, $task['lastCompletedBy']);

        $fiveSecondsAgo = time() - 5;
        $this->assertGreaterThan($fiveSecondsAgo, $task['lastCompletedAt']);
    }

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "887ca7d3-27e3-4964-8378-0f3d0d4aa6d3"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6"]
     */
    public function a_task_cannot_be_completed_if_it_has_not_been_created(string $taskId, string $userId) : void
    {
        $completeTask = new CompleteTask($this->eventStore, new GetById($this->eventStream), $this->getUserById);

        $this->expectException(TaskNotFound::class);

        $completeTask->execute(TaskId::fromString($taskId), UserId::fromString($userId));
    }

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "887ca7d3-27e3-4964-8378-0f3d0d4aa6d0"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "1a6d2a28-e9ca-4695-875d-f80ab4c9b8d0"]
     */
    public function a_task_cannot_be_completed_by_an_unregistered_user(string $taskId, string $userId) : void
    {
        $this->givenTaskAlreadyExists($taskId, 'The name of the task', 50);

        $completeTask = new CompleteTask($this->eventStore, new GetById($this->eventStream), $this->getUserById);

        $this->expectException(UserNotFound::class);

        $completeTask->execute(TaskId::fromString($taskId), UserId::fromString($userId));
    }
}