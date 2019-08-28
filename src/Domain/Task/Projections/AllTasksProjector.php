<?php

namespace jjok\TodoTwo\Domain\Task\Projections;

use jjok\TodoTwo\Domain\EventStream;
use jjok\TodoTwo\Domain\Task\Event as TaskEvent;
use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;

final class AllTasksProjector
{
    public function __construct(EventStream $eventStream, AllTasksStorage $storage)
    {
        $this->eventStream = $eventStream;
        $this->storage = $storage;
    }

    private $eventStream, $storage;
    private $tasks = [];

    /** @throws InvalidEventStream */
    public function rebuild() : void
    {
        $this->tasks = [];

        foreach ($this->eventStream->all() as $event) {
            switch (get_class($event)) {
                case TaskWasCreated::class:
                    $this->applyTaskWasCreated($event);
                    break;

                case TaskWasCompleted::class:
                    $this->applyTaskWasCompleted($event);
                    break;

                case TaskWasRenamed::class:
                    $this->applyTaskWasRenamed($event);
                    break;

                case TaskPriorityWasChanged::class:
                    $this->applyTaskPriorityWasChanged($event);
                    break;
            }
        }

        $this->storage->save(array_values($this->tasks));
    }

    /** @throws InvalidEventStream */
    private function applyTaskWasCreated(TaskWasCreated $event) : void
    {
        $this->assertTaskDoesNotAlreadyExist($event);

        $taskId = $event->taskId();

        $this->tasks[$taskId] = array(
            'id' => $taskId,
            'name' => $event->name(),
            'priority' => $event->priority(),
            'lastCompletedAt' => null,
            'lastCompletedBy' => null,
        );
    }

    /** @throws InvalidEventStream */
    private function assertTaskDoesNotAlreadyExist(TaskWasCreated $event) : void
    {
        if(isset($this->tasks[$event->taskId()])) {
            throw InvalidEventStream::taskAlreadyExists($event);
        }
    }

    /** @throws InvalidEventStream */
    public function applyTaskWasCompleted(TaskWasCompleted $event) : void
    {
        $this->assertTaskAlreadyExists($event);

        $taskId = $event->taskId();

        $this->tasks[$taskId]['lastCompletedAt'] = $event->timestamp();
        $this->tasks[$taskId]['lastCompletedBy'] = $event->by();
    }

    /** @throws InvalidEventStream */
    private function applyTaskWasRenamed(TaskWasRenamed $event) : void
    {
        $this->assertTaskAlreadyExists($event);

        $taskId = $event->taskId();

        $this->tasks[$taskId]['name'] = $event->to();
    }

    /** @throws InvalidEventStream */
    private function applyTaskPriorityWasChanged(TaskPriorityWasChanged $event) : void
    {
        $this->assertTaskAlreadyExists($event);

        $taskId = $event->taskId();

        $this->tasks[$taskId]['priority'] = $event->to();
    }

    /** @throws InvalidEventStream */
    private function assertTaskAlreadyExists(TaskEvent $event) : void
    {
        if(!isset($this->tasks[$event->taskId()])) {
            throw InvalidEventStream::taskDoesNotExist($event);
        }
    }

    /**
     * @deprecated Load projection from storage
     */
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
