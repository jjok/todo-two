<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Query\GetById;
use jjok\TodoTwo\Domain\Task\Query\TaskNotFound;

final class CompleteTaskTest extends CommandTest
{
    /**
     * @test
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "Jonathan"    ]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "Someone Else"]
     */
    public function a_task_can_be_completed(string $id, string $by) : void
    {
        $this->givenTaskAlreadyExists($id, 'The name of the task', 50);

        $completeTask = new CompleteTask($this->eventStore2, new GetById($this->eventStore2));
        $completeTask->execute($id, $by);

        $this->assertTaskWasRecentlyCompleted($id, $by);
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
     * @testWith ["4ef9c809-3e53-4341-a32f-cf3249df65cc", "Jonathan"    ]
     *           ["4ef9c809-3e53-4341-a32f-cf3249df65dd", "Someone Else"]
     */
    public function a_task_cannot_be_completed_if_it_has_not_been_created(string $id, string $by) : void
    {
        $completeTask = new CompleteTask($this->eventStore2, new GetById($this->eventStore2));

        $this->expectException(TaskNotFound::class);

        $completeTask->execute($id, $by);
    }
}