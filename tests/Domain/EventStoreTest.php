<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
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

        $eventStore->push($taskWasCreated);
//        $eventStore->push($taskWasCompleted);

        $file->rewind();

        $serialisedEvent = SerialisedEvent::fromJson($file->current());

        $this->assertEquals($taskWasCreated, $serialisedEvent->toEvent());

//        $file->next();
//        print_r($file->current());

    }
}
