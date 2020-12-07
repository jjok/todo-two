<?php

namespace jjok\TodoTwo\Domain\Task\Projections;

interface AllTasksStorage
{
    public function save(array $allTasks/*, int $version*/) : void;

    public function load() : array;

//    public function version() : int;
}
