<?php

namespace jjok\TodoTwo\Domain\Task\Projections;

interface AllTasksStorage
{
    public function save(array $allTasks) : void;

    public function load() : array;
}
