<?php

namespace jjok\TodoTwo\Domain;

interface EventStore
{
    public function push(Event $event) : void;
}
