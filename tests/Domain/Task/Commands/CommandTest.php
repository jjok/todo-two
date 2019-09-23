<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\EventStore2;
use jjok\TodoTwo\Domain\ProjectionBuildingEventStore;
use jjok\TodoTwo\Domain\Task\Projections\AllTasksProjector;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use jjok\TodoTwo\Infrastructure\File\EventStream;
use jjok\TodoTwo\Infrastructure\File\TempAllTasksStorage;
use PHPUnit\Framework\TestCase;

abstract class CommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $file = new \SplTempFileObject();
        $this->projection = new TempAllTasksStorage();
        $this->eventStore = new ProjectionBuildingEventStore(new EventStore($file), new AllTasksProjector($this->projection));
        $this->eventStream = new EventStream($file);
        $this->eventStore2 = new EventStore2(
            $this->eventStore,
            $this->eventStream
        );
    }

    private $projection;
    protected $eventStore;
    protected $eventStream;
    protected $eventStore2;

    protected function givenTaskAlreadyExists(string $id, string $name, int $priority) : void
    {
        $createTask = new CreateTask($this->eventStore2);

        $createTask->execute($id, $name, $priority);
    }

    protected function getStoredTask(string $id) : array
    {
        $allTasks = $this->projection->load();

        foreach ($allTasks as $task) {
            if( $task['id'] === $id
            ) {
                return $task;
            }
        }

        $this->fail(sprintf('Task "%s" does not exist.', $id));
    }
}
