<?php

namespace jjok\TodoTwo\Domain;

interface Event
{
    public function timestamp() : int;

    public function payload() : array;
}
