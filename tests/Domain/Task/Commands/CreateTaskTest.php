<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

final class CreateTaskTest extends CommandTest
{
    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "The name of the task"    , 10]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "The name of another task", 40]
     */
    public function a_task_can_be_created(string $id, string $name, int $priority) : void
    {
        $createTask = new CreateTask($this->eventStore2);

        $createTask->execute($id, $name, $priority);

        $this->assertTaskWasCreated($id, $name, $priority);
    }

    private function assertTaskWasCreated(string $id, string $name, int $priority) : void
    {
        $task = $this->getStoredTask($id);

        $this->assertSame($name, $task['name']);
        $this->assertSame($priority, $task['priority']);
    }

//    /**
//     * @test
//     * @testWith ["The name of the task"    , 10]
//     *           ["The name of another task", 40]
//     */
//    public function task_cannot_be_created_if_it_already_exists(string $name, int $priority) : void
//    {
//        $command = new CreateTask($this->eventStore);
//        $command->execute($name, $priority);
//
//        $this->expectException(TaskAlreadyExists::class);
//
//        $command->execute($name, $priority);
//    }
}
