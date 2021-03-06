<?php declare(strict_types=1);

namespace jjok\TodoTwo\Infrastructure\File;

use jjok\TodoTwo\Domain\Event;
use jjok\TodoTwo\Domain\SerialisedEvent;

final class EventStore implements \jjok\TodoTwo\Domain\EventStore
{
    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
    }

    private $file;

    public function push(Event ...$events): void
    {
        foreach($events as $event) {
            $serialisedEvent = SerialisedEvent::fromEvent($event);

            $this->file->fwrite($serialisedEvent->toJson() . PHP_EOL);
        }
    }
}
