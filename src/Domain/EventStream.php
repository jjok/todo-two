<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Id as TaskId;

interface EventStream
{
    /**
     * @return Event[]
     */
    public function all() : iterable;

    /**
     * @return Event[]
     */
    public function filterByTaskId(TaskId $id) : iterable;
}
