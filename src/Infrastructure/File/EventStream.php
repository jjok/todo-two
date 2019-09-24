<?php declare(strict_types=1);

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\SerialisedEvent;
use jjok\TodoTwo\Domain\Task\Event as TaskEvent;
use jjok\TodoTwo\Domain\Task\Id as TaskId;
use SplFileObject;

final class EventStream implements \jjok\TodoTwo\Domain\EventStream
{
    public function __construct(SplFileObject $file)
    {
        $file->setFlags(
            SplFileObject::DROP_NEW_LINE |
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY
        );
        $this->file = $file;
    }

    private $file;

    public function all() : iterable
    {
        foreach ($this->file as $line) {
            $serialisedEvent = SerialisedEvent::fromJson($line);

            yield $serialisedEvent->toEvent();
        }
    }

    public function filterByTaskId(TaskId $id) : iterable
    {
        foreach ($this->file as $line) {
            $serialisedEvent = SerialisedEvent::fromJson($line);
            $event = $serialisedEvent->toEvent();

            if($event instanceof TaskEvent && $event->taskId() === $id->toString()) {
                yield $event;
            }
        }
    }
}
