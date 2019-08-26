<?php

namespace jjok\TodoTwo\Domain\Task\Query;

use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Priority;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use jjok\TodoTwo\Infrastructure\File\EventStream;
use PHPUnit\Framework\TestCase;

final class GetByIdTest extends TestCase
{
    /**
     * @test
     * @dataProvider eventProvider
     */
    public function something(array $events, string $expectedId, string $expectedName) : void
    {
        $file = new \SplTempFileObject();
        $eventStore = new EventStore($file);
        $eventStore->push(...$events);

        $query = new GetById(new EventStream($file));

        $task = $query->execute('4ef9c809-3e53-4341-a32f-cf3249df65cc');

        $this->assertSame($expectedId, $task->id());
        $this->assertSame($expectedName, $task->name());
    }

    public function eventProvider() : array
    {
        $task1WasCreated = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );
        $task1WasCompleted = new TaskWasCompleted(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc')->toString(),
            'Jonathan',
            123456789
        );
        $task1WasRenamed = TaskWasRenamed::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'A different name'
        );
        $task1PriorityWasChanged = TaskPriorityWasChanged::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            Priority::fromInt(20)
        );
        $task2WasCreated = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            'The name of another task',
            Priority::fromInt(60)
        );
        $task2WasCompleted = new TaskWasCompleted(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd')->toString(),
            'Someone Else',
            234567890
        );
        $task2WasRenamed = TaskWasRenamed::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            'A second task'
        );
        $task2PriorityWasChanged = TaskPriorityWasChanged::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65dd'),
            Priority::fromInt(99)
        );

        return [
            [
                [$task1WasCreated],
                '4ef9c809-3e53-4341-a32f-cf3249df65cc',
                'The name of the task',
//                50,
//                null,
//                null,
            ],
//            [[$task1WasCreated, $task2WasCreated], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'The name of the task',
//                    'priority' => 50,
//                    'lastCompletedAt' => null,
//                    'lastCompletedBy' => null,
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 60,
//                    'lastCompletedAt' => null,
//                    'lastCompletedBy' => null,
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'The name of the task',
//                    'priority' => 50,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 60,
//                    'lastCompletedAt' => null,
//                    'lastCompletedBy' => null,
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted, $task1WasRenamed], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'A different name',
//                    'priority' => 50,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 60,
//                    'lastCompletedAt' => null,
//                    'lastCompletedBy' => null,
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted, $task1WasRenamed, $task1PriorityWasChanged], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'A different name',
//                    'priority' => 20,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 60,
//                    'lastCompletedAt' => null,
//                    'lastCompletedBy' => null,
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted, $task1WasRenamed, $task1PriorityWasChanged, $task2WasCompleted], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'A different name',
//                    'priority' => 20,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 60,
//                    'lastCompletedAt' => 234567890,
//                    'lastCompletedBy' => 'Someone Else',
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted, $task1WasRenamed, $task1PriorityWasChanged, $task2WasCompleted, $task2PriorityWasChanged], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'A different name',
//                    'priority' => 20,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'The name of another task',
//                    'priority' => 99,
//                    'lastCompletedAt' => 234567890,
//                    'lastCompletedBy' => 'Someone Else',
//                ),
//            ]],
//            [[$task1WasCreated, $task2WasCreated, $task1WasCompleted, $task1WasRenamed, $task1PriorityWasChanged, $task2WasCompleted, $task2PriorityWasChanged, $task2WasRenamed], [
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65cc',
//                    'name' => 'A different name',
//                    'priority' => 20,
//                    'lastCompletedAt' => 123456789,
//                    'lastCompletedBy' => 'Jonathan',
//                ),
//                array(
//                    'id' => '4ef9c809-3e53-4341-a32f-cf3249df65dd',
//                    'name' => 'A second task',
//                    'priority' => 99,
//                    'lastCompletedAt' => 234567890,
//                    'lastCompletedBy' => 'Someone Else',
//                ),
//            ]],
        ];
    }
}
