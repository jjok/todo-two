<?php

namespace jjok\TodoTwo\Domain;

interface EventStream
{
    /**
     * @return Event[]|\Generator
     */
    public function all() : \Traversable;

//    /**
//     * @return Event[]|\Generator
//     */
//    public function filterByTaskId();
}