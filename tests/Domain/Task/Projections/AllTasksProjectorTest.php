<?php

namespace jjok\TodoTwo\Domain\Task\Projections;

use jjok\TodoTwo\Domain\Event;
use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Priority;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use jjok\TodoTwo\Infrastructure\File\EventStream;
use jjok\TodoTwo\Infrastructure\File\TempAllTasksStorage;
use PHPUnit\Framework\TestCase;

final class AllTasksProjectorTest extends TestCase
{
    private function task1WasCreated() : TaskWasCreated
    {
        return TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );
    }

    private function task1WasCompleted() : TaskWasCompleted
    {
        return new TaskWasCompleted(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc')->toString(),
            'Jonathan',
            123456789
        );
    }

    private function task1WasRenamed() : TaskWasRenamed
    {
        return TaskWasRenamed::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'A different name'
        );
    }

    private function task1PriorityWasChanged() : TaskPriorityWasChanged
    {
        return TaskPriorityWasChanged::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            Priority::fromInt(20)
        );
    }

    private function task2WasCreated() : TaskWasCreated
    {
        return TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            'The name of another task',
            Priority::fromInt(60)
        );
    }

    private function task2WasCompleted() : TaskWasCompleted
    {
        return new TaskWasCompleted(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd')->toString(),
            'Someone Else',
            234567890
        );
    }

    private function task2WasRenamed() : TaskWasRenamed
    {
        return TaskWasRenamed::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            'A second task'
        );
    }

    private function task2PriorityWasChanged() : TaskPriorityWasChanged
    {
        return TaskPriorityWasChanged::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            Priority::fromInt(99)
        );
    }

    /** @test */
    public function projection_is_initially_empty() : void
    {
        $storage = new TempAllTasksStorage();
        $projection = new AllTasksProjector($storage);

        $this->assertEquals([], $storage->load());
    }

    public function eventProvider() : array
    {
        return [
            [[
                $this->task1WasCreated(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'The name of the task',
                    'priority' => 50,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                )
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'The name of the task',
                    'priority' => 50,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 60,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'The name of the task',
                    'priority' => 50,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 60,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
                $this->task1WasRenamed(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'A different name',
                    'priority' => 50,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 60,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
                $this->task1WasRenamed(),
                $this->task1PriorityWasChanged(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'A different name',
                    'priority' => 20,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 60,
                    'lastCompletedAt' => null,
                    'lastCompletedBy' => null,
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
                $this->task1WasRenamed(),
                $this->task1PriorityWasChanged(),
                $this->task2WasCompleted(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'A different name',
                    'priority' => 20,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 60,
                    'lastCompletedAt' => 234567890,
                    'lastCompletedBy' => 'Someone Else',
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
                $this->task1WasRenamed(),
                $this->task1PriorityWasChanged(),
                $this->task2WasCompleted(),
                $this->task2PriorityWasChanged(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'A different name',
                    'priority' => 20,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'The name of another task',
                    'priority' => 99,
                    'lastCompletedAt' => 234567890,
                    'lastCompletedBy' => 'Someone Else',
                ),
            ]],
            [[
                $this->task1WasCreated(),
                $this->task2WasCreated(),
                $this->task1WasCompleted(),
                $this->task1WasRenamed(),
                $this->task1PriorityWasChanged(),
                $this->task2WasCompleted(),
                $this->task2PriorityWasChanged(),
                $this->task2WasRenamed(),
            ], [
                '4ef9c809-3e53-4341-a32f-cf3249df65cc' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                    'name' => 'A different name',
                    'priority' => 20,
                    'lastCompletedAt' => 123456789,
                    'lastCompletedBy' => 'Jonathan',
                ),
                '4ef9c809-3e53-4341-a32f-cf3249df65dd' => array(
                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
                    'name' => 'A second task',
                    'priority' => 99,
                    'lastCompletedAt' => 234567890,
                    'lastCompletedBy' => 'Someone Else',
                ),
            ]],
        ];
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function projection_can_be_built_from_events(array $events, array $expectedProjection) : void
    {
        $storage = new TempAllTasksStorage();
        $projection = new AllTasksProjector($storage);
        $projection->apply($events);

        $this->assertEquals($expectedProjection, $storage->load());
    }

    /** @test */
    public function a_task_can_not_be_created_twice() : void
    {
        $createEvent = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );

        $projection = new AllTasksProjector(new TempAllTasksStorage());

        $projection->apply([$createEvent]);

        $this->expectException(InvalidEventStream::class);
        $this->expectExceptionMessage(
            'Can not create task "The name of the task" with ID "4ef9c809-3e53-4341-a32f-cf3249df65cc" as it already exists'
        );

        $projection->apply([$createEvent]);
    }

    public function invalidEventProvider() : array
    {
        return [
            [TaskWasCompleted::with(
                Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
                'Jonathan'
            )],
            [TaskWasRenamed::with(
                Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
                'A different name'
            )],
            [TaskPriorityWasChanged::with(
                Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
                Priority::fromInt(20)
            )],
        ];
    }

    /**
     * @test
     * @dataProvider invalidEventProvider
     */
    public function events_can_not_be_applied_to_a_task_that_does_not_yet_exist(Event $event) : void
    {
        $projection = new AllTasksProjector(new TempAllTasksStorage());

        $this->expectException(InvalidEventStream::class);
        $this->expectExceptionMessage(
            'can not be applied as task with ID "4ef9c809-3e53-4341-a32f-cf3249df65cc" does not exist'
        );

        $projection->apply([$event]);
    }

    /**
     * @test
     * @dataProvider eventProvider
     */
    public function projection_can_be_rebuilt_from_event_stream(array $events, array $expectedProjection) : void
    {
        $file = new \SplTempFileObject();
        $storage = new TempAllTasksStorage();
        $projection = new AllTasksProjector($storage);
        $eventStore = new EventStore($file);
        $eventStream = new EventStream($file);

        $eventStore->push(...$events);

        $projection->apply([$this->task1WasCreated(), $this->task2WasCreated()]);

        $projection->rebuild($eventStream);

        $this->assertEquals($expectedProjection, $storage->load());
    }
}
