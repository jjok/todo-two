<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Events;

use jjok\TodoTwo\Domain\Task\Event;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Priority;

final class TaskWasCreated implements Event
{
    public static function with(Id $id, string $name, Priority $priority) : self
    {
        return new self($id->toString(), $name, $priority->toInt(), time());
    }

    public function __construct(string $id, string $name, int $priority, int $timestamp)
    {
        $this->id = $id;
        $this->name = $name;
        $this->priority = $priority;
        $this->timestamp = $timestamp;
    }

    private $id, $name, $priority, $timestamp;

    public function taskId() : string
    {
        return $this->id;
    }

    public function name() : string
    {
        return $this->name;
    }

    public function priority() : int
    {
        return $this->priority;
    }

    public function timestamp() : int
    {
        return $this->timestamp;
    }
}
