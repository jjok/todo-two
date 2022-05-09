<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;

final class TaskWasCompleted implements Event
{
    public static function with(Id $taskId, string $by) : self
    {
        return new self($taskId->toString(), $by, time());
    }

    public function __construct(string $taskId, string $by, int $timestamp)
    {
        $this->taskId = $taskId;
        $this->by = $by;
        $this->timestamp = $timestamp;
    }

    private string $taskId, $by;
    private int $timestamp;

    public function taskId() : string
    {
        return $this->taskId;
    }

    public function by() : string
    {
        return $this->by;
    }

    public function timestamp() : int
    {
        return $this->timestamp;
    }

    public function payload(): array
    {
        return array(
            'taskId' => $this->taskId(),
            'userId' => $this->by(),
            'timestamp' => $this->timestamp(),
        );
    }
}
