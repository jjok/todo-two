<?php

namespace jjok\TodoTwo\Domain\Task\Commands;

use jjok\TodoTwo\Domain\ProjectionBuildingEventStore;
use jjok\TodoTwo\Domain\Task\Projections\AllTasksProjector;
use jjok\TodoTwo\Domain\User;
use jjok\TodoTwo\Domain\User\Id as UserId;
use jjok\TodoTwo\Infrastructure\File\EventStore;
use jjok\TodoTwo\Infrastructure\File\EventStream;
use jjok\TodoTwo\Infrastructure\File\TempAllTasksStorage;
use jjok\TodoTwo\Infrastructure\InMemory\GetUserById;
use PHPUnit\Framework\TestCase;

abstract class CommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->getUserById = new GetUserById(
            new User(UserId::fromString('887ca7d3-27e3-4964-8378-0f3d0d4aa6d3'), 'Jonathan'),
            new User(UserId::fromString('1a6d2a28-e9ca-4695-875d-f80ab4c9b8d6'), 'Someone Else')
        );
        $file = new \SplTempFileObject();
        $this->projection = new TempAllTasksStorage();
        $this->eventStore = new ProjectionBuildingEventStore(
            new EventStore($file),
            new AllTasksProjector($this->projection, $this->getUserById)
        );
        $this->eventStream = new EventStream($file);
    }

    private $projection;
    protected $getUserById;
    protected $eventStore;
    protected $eventStream;

    protected function givenTaskAlreadyExists(string $id, string $name, int $priority) : void
    {
        $createTask = new CreateTask($this->eventStore);

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
