<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task\Projections;

use jjok\TodoTwo\Domain\EventStream;
use jjok\TodoTwo\Domain\Task\Event as TaskEvent;
use jjok\TodoTwo\Domain\Task\Events\TaskPriorityWasChanged;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCompleted;
use jjok\TodoTwo\Domain\Task\Events\TaskWasCreated;
use jjok\TodoTwo\Domain\Task\Events\TaskWasRenamed;
//use jjok\TodoTwo\Domain\User\Id;
//use jjok\TodoTwo\Domain\User\Query\GetUserById;

final class AllTasksProjector
{
    public function __construct(AllTasksStorage $storage/*, GetUserById $getUserById*/)
    {
        $this->storage = $storage;
//        $this->getUserById = $getUserById;
    }

    private $storage, $getUserById;
    private $tasks = [];

    /** @throws InvalidEventStream */
    public function apply(iterable $events) : void
    {
        $this->tasks = $this->storage->load();
//        $version = $this->storage->version();

        foreach ($events as $event) {
            $this->applyEvent($event);
//            $version++;
        }

        $this->storage->save($this->tasks/*, $version*/);
    }

    public function rebuild(EventStream $eventStream) : void
    {
        $this->tasks = [];

        foreach ($eventStream->all() as $event) {
            /** @var TaskEvent $event Seems a bit shady. I've probably got something wrong here. */
            $this->applyEvent($event);
        }

        $this->storage->save($this->tasks);
    }

    /** @throws InvalidEventStream */
    private function applyEvent(TaskEvent $event) : void
    {
        switch (get_class($event)) {
            case TaskWasCreated::class:
                /** @var TaskWasCreated $event */
                $this->applyTaskWasCreated($event);
                break;

            case TaskWasCompleted::class:
                /** @var TaskWasCompleted $event */
                $this->applyTaskWasCompleted($event);
                break;

            case TaskWasRenamed::class:
                /** @var TaskWasRenamed $event */
                $this->applyTaskWasRenamed($event);
                break;

            case TaskPriorityWasChanged::class:
                /** @var TaskPriorityWasChanged $event */
                $this->applyTaskPriorityWasChanged($event);
                break;
        }
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
    private function applyTaskWasCompleted(TaskWasCompleted $event) : void
    {
        $this->assertTaskAlreadyExists($event);

        $taskId = $event->taskId();

//        $this->getUserById->execute(Id::fromString());

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
}

final class InvalidEventStream extends \Exception
{
    public static function taskAlreadyExists(TaskWasCreated $event) : self
    {
        return new self(sprintf(
            'Can not create task "%s" with ID "%s" as it already exists.',
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
