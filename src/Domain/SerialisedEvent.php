<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;

final class SerialisedEvent
{
    public static function fromJson(string $json) : self
    {
        $json = json_decode($json, true);

        return new self($json['name'], $json['payload']);
    }

    public static function fromEvent(Event $event) : self
    {
        return new self(get_class($event), array(
            'taskId' => $event->taskId(),
            'name' => $event->name(),
            'priority' => $event->priority(),
            'timestamp' => $event->timestamp(),
        ));
    }

    public function __construct(string $eventName, array $payload)
    {
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    private $eventName, $payload;

    public function toJson() : string
    {
        return json_encode(array(
            'name' => $this->eventName,
            'payload' => $this->payload,
        ));
    }

    public function toEvent() : Event
    {
//        switch ($this->eventName) {
//            case TaskWasCreated::class:
                return new TaskWasCreated(
                    $this->payload['taskId'],
                    $this->payload['name'],
                    $this->payload['priority'],
                    $this->payload['timestamp']
                );
//        }
    }
}
