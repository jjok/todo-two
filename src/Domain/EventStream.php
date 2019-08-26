<?php

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\Task\Id as TaskId;

interface EventStream
{
    /**
     * @return Event[]|\Generator
     */
    public function all() : \Traversable;

    /**
     * @return Event[]|\Generator
     */
    public function filterByTaskId(TaskId $id) : \Traversable;
}
