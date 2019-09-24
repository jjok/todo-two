<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain;

trait EmitsEvents
{
    protected function recordThat(Event $occurred) : void
    {
        $this->events[] = $occurred;
    }

    private $events = [];

    public function releaseEvents() : array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}
