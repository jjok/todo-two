<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Id as TaskId;

//TODO This better?
//final class EventStore2 implements EventStore, EventStream
//{
//    public function __construct(EventStore $eventStore, EventStream $eventStream)
//    {
//        $this->eventStore = $eventStore;
//        $this->eventStream = $eventStream;
//    }
//
//    private $eventStore, $eventStream;
//
//    public function push(Event ...$events): void
//    {
//        $this->eventStore->push(...$events);
//    }
//
//    public function all(): iterable
//    {
//        return $this->eventStream->all();
//    }
//
//    public function filterByTaskId(TaskId $id): iterable
//    {
//        return $this->eventStream->filterByTaskId($id);
//    }
//}
