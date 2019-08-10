<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Events\TaskWasRenamed;
use jjok\TodoTwo\Domain\Task\Id;
use jjok\TodoTwo\Domain\Task\Event;

final class Task
{
    use EmitsEvents;

    public static function create(string $name, int $priority) : self
    {
        $id = Id::generate();
        $taskWasCreated = TaskWasCreated::with($id, $name, $priority);
        $task = self::fromEvents($taskWasCreated);
        $task->recordThat($taskWasCreated);

        return $task;
    }

    public static function fromEvents(Event ...$events) : self
    {
        $taskWasCreated = array_shift($events);
        $task = new self($taskWasCreated);

        foreach ($events as $event) {
            $task->apply($event);
        }

        return $task;
    }

    private function __construct(TaskWasCreated $taskWasCreated)
    {
        $this->id = Id::fromString($taskWasCreated->taskId());
        $this->name = $taskWasCreated->name();
        $this->priority = $taskWasCreated->priority();
    }

    private $id, $name, $priority;
    private $lastCompletedAt, $lastCompletedBy;

    public function complete(string $by) : void
    {
        $taskWasCompleted = TaskWasCompleted::with($this->id, $by);

        $this->recordThat($taskWasCompleted);
        $this->apply($taskWasCompleted);
    }

    public function rename(string $to) : void
    {
        $taskWasRenamed = TaskWasRenamed::with($this->id, $to);

        $this->recordThat($taskWasRenamed);
        $this->apply($taskWasRenamed);
    }

    public function updatePriority(int $to) : void
    {
        $taskPriorityWasChanged = TaskPriorityWasChanged::with($this->id, $to);

        $this->recordThat($taskPriorityWasChanged);
        $this->apply($taskPriorityWasChanged);
    }

    private function apply(Event $event) : void
    {
        if($event->taskId() !== $this->id->toString()) {
            throw new \InvalidArgumentException(sprintf(
                'Event %s for task %s can not be applied to task %s.',
                get_class($event),
                $event->taskId(),
                $this->id->toString()
            ));
        }

        switch(get_class($event)) {
            case TaskWasCompleted::class:
                /** @var TaskWasCompleted $event */
                $this->lastCompletedBy = $event->by();
                $this->lastCompletedAt = $event->timestamp();
                break;

            case TaskWasRenamed::class:
                /** @var TaskWasRenamed $event */
                $this->name = $event->to();
                break;

            case TaskPriorityWasChanged::class:
                /** @var TaskPriorityWasChanged $event */
                $this->priority = $event->to();
                break;

            default:
                throw new \InvalidArgumentException(
                    sprintf('Unexpected event. %s can not be applied to %s', get_class($event), __CLASS__)
                );
        }
    }
}
