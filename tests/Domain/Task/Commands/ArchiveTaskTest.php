<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Id as TaskId;
use jjok\TodoTwo\Domain\Task\Query\GetById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class ArchiveTaskTest extends CommandTest
{
    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee"]
     */
    public function a_task_can_be_archived(string $taskId) : void
    {
        $this->givenTaskAlreadyExists($taskId, 'Task name', random_int(1, 99));

        $archiveTask = new ArchiveTask($this->eventStore, new GetById($this->eventStream));
        $archiveTask->execute(TaskId::fromString($taskId));

        $this->assertTaskWasArchived($taskId);
    }

    private function assertTaskWasArchived(string $id) : void
    {
        $task = $this->getStoredTask($id);

        self::assertTrue($task['isArchived']);
    }

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee"]
     */
    public function a_task_cannot_be_archived_if_it_does_not_exist(string $taskId) : void
    {
        $archiveTask = new ArchiveTask($this->eventStore, new GetById($this->eventStream));

        $this->expectException(TaskNotFound::class);

        $archiveTask->execute(TaskId::fromString($taskId));
    }
}
