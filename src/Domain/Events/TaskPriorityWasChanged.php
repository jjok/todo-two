<?php

namespace jjok\TodoTwo\Domain\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;

final class TaskPriorityWasChanged implements Event
{
    public static function with(Id $taskId, int $newPriority) : self
    {
        return new self($taskId->toString(), $newPriority, time());
    }

    public function __construct(string $taskId, string $to, int $timestamp)
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
}
