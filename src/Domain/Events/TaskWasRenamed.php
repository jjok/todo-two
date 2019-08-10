<?php

namespace jjok\TodoTwo\Domain\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;

final class TaskWasRenamed implements Event
{
    public static function with(Id $taskId, string $newName) : self
    {
        return new self($taskId->toString(), $newName, time());
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

    public function to() : string
    {
        return $this->to;
    }

    public function timestamp() : int
    {
        return $this->timestamp;
    }
}
