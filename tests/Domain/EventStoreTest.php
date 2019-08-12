<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Priority;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use PHPUnit\Framework\TestCase;

final class EventStoreTest extends TestCase
{
    /** @test */
    public function something() : void
    {
        $file = new \SplTempFileObject();
        $eventStore = new EventStore($file);

        $taskWasCreated = TaskWasCreated::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'The name of the task',
            Priority::fromInt(50)
        );
        $taskWasCompleted = TaskWasCompleted::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'Jonathan'
        );
        $taskWasRenamed = TaskWasRenamed::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            'A different name'
        );
        $taskPriorityWasChanged = TaskPriorityWasChanged::with(
            Id::fromString('4ef9c809-3e53-4341-a32f-cf3249df65cc'),
            Priority::fromInt(20)
        );

        $eventStore->push($taskWasCreated);
        $eventStore->push($taskWasCompleted);
        $eventStore->push($taskWasRenamed);
        $eventStore->push($taskPriorityWasChanged);

        $file->rewind();

        $this->assertNextEventEquals($taskWasCreated, $file);
        $this->assertNextEventEquals($taskWasCompleted, $file);
        $this->assertNextEventEquals($taskWasRenamed, $file);
        $this->assertNextEventEquals($taskPriorityWasChanged, $file);
    }

    private function assertNextEventEquals(Event $expectedEvent, \SplFileObject $file) : void
    {
        $serialisedEvent = SerialisedEvent::fromJson($file->current());

        $this->assertEquals($expectedEvent, $serialisedEvent->toEvent());

        $file->next();
    }
}
