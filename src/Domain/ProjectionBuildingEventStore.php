<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Projections\AllTasksProjector;

final class ProjectionBuildingEventStore implements EventStore
{
    public function __construct(EventStore $eventStore, AllTasksProjector $allTasksProjector)
    {
        $this->eventStore = $eventStore;
        $this->projector = $allTasksProjector;
    }

    private EventStore $eventStore;
    private AllTasksProjector $projector;

    public function push(Event ...$events): void
    {
        $this->eventStore->push(...$events);

        $this->projector->apply($events);
    }
}
