<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Id as TaskId;

//TODO This better?
final class EventStore2 implements EventStore, EventStream
{
    public function __construct(EventStore $eventStore, EventStream $eventStream/*, AllTasksProjector $projector*/)
    {
        $this->eventStore = $eventStore;
        $this->eventStream = $eventStream;
//        $this->projector = $projector;
    }

    private $eventStore, $eventStream;
//    private $projector;

    public function push(Event ...$events): void
    {
        $this->eventStore->push(...$events);

//        $this->projector->apply($events);
    }

    public function all(): \Traversable
    {
        return $this->eventStream->all();
    }

    public function filterByTaskId(TaskId $id): \Traversable
    {
        return $this->eventStream->filterByTaskId($id);
    }
}
