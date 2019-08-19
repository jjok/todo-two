<?php

namespace jjok\TodoTwo\Infrastructure\File;
use jjok\TodoTwo\Domain\Event;
use jjok\TodoTwo\Domain\SerialisedEvent;

final class EventStream implements \jjok\TodoTwo\Domain\EventStream
{
    public function __construct(\SplFileObject $file)
    {
        $file->setFlags(\SplFileObject::DROP_NEW_LINE);
        $this->file = $file;
    }

    private $file;

    /**
     * @return Event[]|\Generator
     */
    public function all() {
        for($this->file->rewind(); !$this->file->eof(); $this->file->next()) {
            $serialisedEvent = SerialisedEvent::fromJson($this->file->current());

            yield $serialisedEvent->toEvent();
        }
    }
}
