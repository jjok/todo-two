<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;

final class TaskWasArchived implements Event
{
    public static function with(Id $taskId) : self
    {
        return new self($taskId->toString(), time());
    }

    public function __construct(string $taskId, int $timestamp)
    {
        $this->taskId = $taskId;
        $this->timestamp = $timestamp;
    }

    private string $taskId;
    private int $timestamp;

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function timestamp() : int
    {
        return $this->timestamp;
    }

    public function payload(): array
    {
        return array(
            'taskId' => $this->taskId(),
            'timestamp' => $this->timestamp(),
        );
    }
}
