<?php

namespace jjok\TodoTwo\Domain\Task\Projections;

use jjok\TodoTwo\Domain\EventStream;
use jjok\TodoTwo\Domain\Task\Event as TaskEvent;
use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;

final class AllTasks
{
    public function __construct(EventStream $eventStream)
    {
        $this->eventStream = $eventStream;
    }

    private $eventStream;
    private $tasks = [];

    /**
     * @throws InvalidEventStream
     */
    public function rebuild() : void
    {
        foreach ($this->eventStream->all() as $event) {
            /** @var TaskEvent $event */
            $taskId = $event->taskId();

            switch (get_class($event)) {
                case TaskWasCreated::class:
                    /** @var TaskWasCreated $event */
                    $this->assertTaskDoesNotAlreadyExist($event);

                    $this->tasks[$taskId] = array(
                        'id' => $taskId,
                        'name' => $event->name(),
                        'priority' => $event->priority(),
                        'lastCompletedAt' => null,
                        'lastCompletedBy' => null,
                    );
                    break;

                case TaskWasCompleted::class:
                    /** @var TaskWasCompleted $event */
                    $this->assertTaskAlreadyExists($event);

                    $this->tasks[$taskId]['lastCompletedAt'] = $event->timestamp();
                    $this->tasks[$taskId]['lastCompletedBy'] = $event->by();
                    break;

                case TaskWasRenamed::class:
                    /** @var TaskWasRenamed $event */
                    $this->assertTaskAlreadyExists($event);

                    $this->tasks[$taskId]['name'] = $event->to();
                    break;

                case TaskPriorityWasChanged::class:
                    /** @var TaskPriorityWasChanged $event */
                    $this->assertTaskAlreadyExists($event);

                    $this->tasks[$taskId]['priority'] = $event->to();
                    break;
            }
        }
    }

    /** @throws InvalidEventStream */
    private function assertTaskDoesNotAlreadyExist(TaskWasCreated $event) : void
    {
        if(isset($this->tasks[$event->taskId()])) {
            throw InvalidEventStream::taskAlreadyExists($event);
        }
    }

    /** @throws InvalidEventStream */
    private function assertTaskAlreadyExists(TaskEvent $event) : void
    {
        if(!isset($this->tasks[$event->taskId()])) {
            throw InvalidEventStream::taskDoesNotExist($event);
        }
    }

    public function toArray() : array
    {
        return array_values($this->tasks);
    }
}

final class InvalidEventStream extends \Exception
{
    public static function taskAlreadyExists(TaskWasCreated $event) : self
    {
        return new self(sprintf(
            'Can not created task "%s" with ID "%s" as it already exists.',
            $event->name(),
            $event->taskId()
        ));
    }

    public static function taskDoesNotExist(TaskEvent $event) : self
    {
        return new self(sprintf(
            'Event "%s" can not be applied as task with ID "%s" does not exist.',
            get_class($event),
            $event->taskId()
        ));
    }
}
