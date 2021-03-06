<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasArchived;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Priority;
use jjok\TodoTwo\Domain\User\Id as UserId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class TaskTest extends TestCase
{
    /** @test */
    public function a_new_task_can_be_created() : void
    {
        $task = Task::create('4ef9c809-3e53-4341-a32f-cf3249df65cc', 'The name of the task', 50);

        $this->assertTaskWasCreated('4ef9c809-3e53-4341-a32f-cf3249df65cc', 'The name of the task', 50, $task);
    }

    private function assertTaskWasCreated(string $id, string $name, int $priority, Task $task) : void
    {
        $events = $task->releaseEvents();

        /** @var TaskWasCreated $taskWasCreated */
        [$taskWasCreated] = $events;

        $this->assertInstanceOf(TaskWasCreated::class, $taskWasCreated);

        $this->assertTaskIdIsValid($taskWasCreated);
        $this->assertSame($id, $taskWasCreated->taskId());
        $this->assertSame($name, $taskWasCreated->name());
        $this->assertSame($priority, $taskWasCreated->priority());
        $this->assertEventHappenedRecently($taskWasCreated);
    }

    private function previouslyCreatedEvent() : Task
    {
        return Task::fromEvents(TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        ));
    }

    /** @test */
    public function an_existing_task_can_be_completed() : void
    {
        $task = $this->previouslyCreatedEvent();
        $userId = UserId::fromString('1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6');
        $userName = 'Jonathan';

        $task->complete($userId, $userName);

        $this->assertTaskWasRecentlyCompleted($userName, $task);
    }

    private function assertTaskWasRecentlyCompleted(string $user, Task $task) : void
    {
        $events = $task->releaseEvents();

        /** @var TaskWasCompleted $taskWasCompleted */
        [$taskWasCompleted] = $events;

        $this->assertInstanceOf(TaskWasCompleted::class, $taskWasCompleted);

        $this->assertTaskIdIsValid($taskWasCompleted);
        $this->assertSame($user, $taskWasCompleted->by());
        $this->assertEventHappenedRecently($taskWasCompleted);
    }

    /** @test */
    public function unexpected_events_can_not_be_applied() : void
    {
        $taskWasCreated = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );
        $unknownEvent = new class implements Event {
            public function taskId() : string
            {
                return '4ef9c809-3e53-4341-a32f-cf3249df65cc';
            }
            public function timestamp(): int
            {
                return 0;
            }
            public function payload(): array
            {
                return [];
            }
        };

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unexpected event');

        Task::fromEvents($taskWasCreated, $unknownEvent);
    }

    /** @test */
    public function events_relating_to_other_tasks_can_not_be_applied() : void
    {
        $taskWasCreated = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );
        $taskWasCompleted = TaskWasCompleted::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cd'),
            'Jonathan'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('can not be applied to task');

        Task::fromEvents($taskWasCreated, $taskWasCompleted);
    }

    /** @test */
    public function an_existing_task_can_be_renamed() : void
    {
        $task = $this->previouslyCreatedEvent();
        $newName = 'The new name for the task';

        $task->rename($newName);

        $this->assertTaskWasRecentlyRenamed($newName, $task);
    }

    private function assertTaskWasRecentlyRenamed(string $newName, Task $task) : void
    {
        $events = $task->releaseEvents();

        /** @var TaskWasRenamed $taskWasRenamed */
        [$taskWasRenamed] = $events;

        $this->assertInstanceOf(TaskWasRenamed::class, $taskWasRenamed);

        $this->assertTaskIdIsValid($taskWasRenamed);
        $this->assertSame($newName, $taskWasRenamed->to());
        $this->assertEventHappenedRecently($taskWasRenamed);
    }

    /**
     * @test
     * @testWith [  1]
     *           [ 29]
     *           [ 70]
     *           [100]
     */
    public function the_priority_of_an_existing_task_can_be_updated(int $priority) : void
    {
        $task = $this->previouslyCreatedEvent();

        $task->updatePriority($priority);

        $this->assertPriorityOfTaskWasRecentlyChanged($priority, $task);
    }

    private function assertPriorityOfTaskWasRecentlyChanged(int $newPriority, Task $task) : void
    {
        $events = $task->releaseEvents();

        /** @var TaskPriorityWasChanged $taskPriorityWasChanged */
        [$taskPriorityWasChanged] = $events;

        $this->assertInstanceOf(TaskPriorityWasChanged::class, $taskPriorityWasChanged);

        $this->assertTaskIdIsValid($taskPriorityWasChanged);
        $this->assertSame($newPriority, $taskPriorityWasChanged->to());
        $this->assertEventHappenedRecently($taskPriorityWasChanged);
    }

    /**
     * @test
     * @testWith [-99]
     *           [ -1]
     *           [  0]
     *           [101]
     *           [999]
     */
    public function a_task_can_only_be_created_with_a_valid_priority(int $priority) : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Priority must be between 1 and 100');

        Task::create('4ef9c809-3e53-4341-a32f-cf3249df65cc', 'The name of the task', $priority);
    }

    /**
     * @test
     * @testWith [-99]
     *           [ -1]
     *           [  0]
     *           [101]
     *           [999]
     */
    public function priority_can_only_be_changed_to_something_valid(int $priority) : void
    {
        $task = $this->previouslyCreatedEvent();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Priority must be between 1 and 100');

        $task->updatePriority($priority);
    }

    /** @test */
    public function an_existing_task_can_archived() : void
    {
        $task = $this->previouslyCreatedEvent();

        $task->archive();

        $this->assertTaskWasRecentlyArchived($task);
    }

    /** @test */
    public function an_archived_task_cannot_be_archived_again() : void
    {
        $task = $this->previouslyCreatedEvent();

        $task->archive();
        $task->archive();

        $this->assertTaskWasRecentlyArchived($task);
    }

    private function assertTaskWasRecentlyArchived(Task $task) : void
    {
        $events = $task->releaseEvents();

        /** @var TaskWasArchived $taskWasArchived */
        [$taskWasArchived] = $events;

        self::assertInstanceOf(TaskWasArchived::class, $taskWasArchived);
        $this->assertEventHappenedRecently($taskWasArchived);
    }

    /** @test */
    public function an_archived_task_cannot_be_completed() : void
    {
        $task = $this->previouslyCreatedEvent();
        $userId = UserId::fromString('1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6');
        $userName = 'Jonathan';

        $this->expectException(AnArchivedTaskCannotBeCompleted::class);

        $task->archive();
        $task->complete($userId, $userName);
    }

    /** @test */
    public function an_archived_task_cannot_be_renamed() : void
    {
        $task = $this->previouslyCreatedEvent();

        $this->expectException(AnArchivedTaskCannotBeChanged::class);

        $task->archive();
        $task->rename('anything');
    }

    /** @test */
    public function an_archived_task_cannot_have_its_priority_changed() : void
    {
        $task = $this->previouslyCreatedEvent();

        $this->expectException(AnArchivedTaskCannotBeChanged::class);

        $task->archive();
        $task->updatePriority(70);
    }

    private function assertTaskIdIsValid(Event $event) : void
    {
        $this->assertTrue(Uuid::isValid($event->taskId()), sprintf('"%s" is not a valid UUID', $event->taskId()));
    }

    private function assertEventHappenedRecently(Event $event) : void
    {
        $fiveSecondsAgo = time() - 5;
        $this->assertGreaterThan($fiveSecondsAgo, $event->timestamp());
    }
}
