<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasArchived;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;

final class SerialisedEvent
{
    public static function fromJson(string $json) : self
    {
        $array = json_decode($json, true);

        return new self($array['name'], $array['payload']);
    }

    public static function fromEvent(Event $event) : self
    {
        return new self(get_class($event), $event->payload());
    }

    public function __construct(string $eventName, array $payload)
    {
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    private string $eventName;
    private array $payload;

    public function toJson() : string
    {
        return json_encode(array(
            'name' => $this->eventName,
            'payload' => $this->payload,
        ));
    }

    public function toEvent() : Event
    {
        switch ($this->eventName) {
            case TaskWasCreated::class:
                return new TaskWasCreated(
                    $this->payload['taskId'],
                    $this->payload['name'],
                    $this->payload['priority'],
                    $this->payload['timestamp']
                );

            case TaskWasCompleted::class:
                return new TaskWasCompleted(
                    $this->payload['taskId'],
                    $this->payload['userId'],
                    $this->payload['timestamp']
                );

            case TaskWasRenamed::class:
                return new TaskWasRenamed(
                    $this->payload['taskId'],
                    $this->payload['to'],
                    $this->payload['timestamp']
                );

            case TaskPriorityWasChanged::class:
                return new TaskPriorityWasChanged(
                    $this->payload['taskId'],
                    $this->payload['to'],
                    $this->payload['timestamp']
                );

            case TaskWasArchived::class:
                return new TaskWasArchived(
                    $this->payload['taskId'],
                    $this->payload['timestamp']
                );
        }

        throw new \InvalidArgumentException(sprintf('%s can not be unserialised', $this->eventName));
    }
}
