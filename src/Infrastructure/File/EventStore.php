<?php

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Event;
use jjok\TodoTwo\Domain\SerialisedEvent;

final class EventStore implements \jjok\TodoTwo\Domain\EventStore
{
    public function __construct(\SplFileObject $file)
    {
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);
//        $this->file = new \SplFileObject($fileName);
        $this->file = $file;
    }

    private $file;

    public function push(Event $event): void
    {
        $serialisedEvent = SerialisedEvent::fromEvent($event);

        $this->file->fwrite($serialisedEvent->toJson() . PHP_EOL);
    }
}