<?php

namespace jjok\TodoTwo\Domain\Task;

use jjok\TodoTwo\Domain\Event as DomainEvent;

interface Event extends DomainEvent
{
    public function taskId() : string;
}
