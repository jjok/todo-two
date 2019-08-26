<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\Task\Projections\AllTasks;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use jjok\TodoTwo\Infrastructure\File\EventStream;
use PHPUnit\Framework\TestCase;

abstract class CommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $file = new \SplTempFileObject();
        $this->eventStore = new EventStore($file);
        $this->eventStream = new EventStream($file);
    }

    protected $eventStore;
    protected $eventStream;

    protected function givenTaskAlreadyExists(string $id, string $name, int $priority) : void
    {
        $createTask = new CreateTask($this->eventStore);

        $createTask->execute($id, $name, $priority);
    }

    protected function getStoredTask(string $id) : array
    {
        $projection = new AllTasks();

        $projection->build($this->eventStream);

        $allTasks = $projection->toArray();

        foreach ($allTasks as $task) {
            if( $task['id'] === $id
            ) {
                return $task;
            }
        }

        $this->fail(sprintf('Task "%s" does not exist.', $id));
    }
}
