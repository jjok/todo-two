<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Priority;

final class TaskPriorityWasChanged implements Event
{
    public static function with(Id $taskId, Priority $newPriority) : self
    {
        return new self($taskId->toString(), $newPriority->toInt(), time());
    }

    public function __construct(string $taskId, int $to, int $timestamp)
    {
        $this->taskId = $taskId;
        $this->to = $to;
        $this->timestamp = $timestamp;
    }

    private $taskId, $to, $timestamp;

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function to() : int
    {
        return $this->to;
    }

    public function timestamp() : int
    {
        return $this->timestamp;
    }

    public function payload(): array
    {
        return array(
            'taskId' => $this->taskId(),
            'to' => $this->to(),
            'timestamp' => $this->timestamp(),
        );
    }
}
