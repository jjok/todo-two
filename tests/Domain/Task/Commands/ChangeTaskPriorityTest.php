<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Query\GetById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class ChangeTaskPriorityTest extends CommandTest
{
    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", 10, 20]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", 90, 55]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee",  1, 99]
     */
    public function a_task_can_have_its_priority_changed(string $id, int $originalPriority, int $newPriority) : void
    {
        $this->givenTaskAlreadyExists($id, 'Task name', $originalPriority);

        $changeTaskPriority = new ChangeTaskPriority($this->eventStore, new GetById($this->eventStream));
        $changeTaskPriority->execute($id, $newPriority);

        $this->assertTaskPriorityWasChanged($id, $newPriority);
    }

    private function assertTaskPriorityWasChanged(string $id, int $newPriority) : void
    {
        $task = $this->getStoredTask($id);

        $this->assertSame($newPriority, $task['priority']);
    }

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", 50]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", 99]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee",  1]
     */
    public function a_task_priority_cannot_be_changed_if_the_task_does_not_exist(string $id, int $newPriority) : void
    {
        $changeTaskPriority = new ChangeTaskPriority($this->eventStore, new GetById($this->eventStream));

        $this->expectException(TaskNotFound::class);

        $changeTaskPriority->execute($id, $newPriority);
    }
}
