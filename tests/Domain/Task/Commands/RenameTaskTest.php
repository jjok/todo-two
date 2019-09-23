<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Query\GetById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class RenameTaskTest extends CommandTest
{
    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "Original name 1", "New name 1"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "Original name 2", "New name 2"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee", "Original name 3", "New name 3"]
     */
    public function a_task_can_be_renamed(string $id, string $originalName, string $newName) : void
    {
        $this->givenTaskAlreadyExists($id, $originalName, 50);

        $renameTask = new RenameTask($this->eventStore, new GetById($this->eventStream));
        $renameTask->execute($id, $newName);

        $this->assertTaskWasRenamed($id, $newName);
    }

    private function assertTaskWasRenamed(string $id, string $newName) : void
    {
        $task = $this->getStoredTask($id);

        $this->assertSame($newName, $task['name']);
    }

    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "New name 1"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "New name 2"]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65ee", "New name 3"]
     */
    public function a_task_cannot_be_renamed_if_it_does_not_exist(string $id, string $newName) : void
    {
        $renameTask = new RenameTask($this->eventStore, new GetById($this->eventStream));

        $this->expectException(TaskNotFound::class);

        $renameTask->execute($id, $newName);
    }
}
