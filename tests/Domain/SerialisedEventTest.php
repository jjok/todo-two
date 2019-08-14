<?php

namespace jjok\TodoTwo\Domain;

use PHPUnit\Framework\TestCase;

final class SerialisedEventTest extends TestCase
{
    /** @test */
    public function only_known_events_can_be_unserialised() : void
    {
        $serialised = SerialisedEvent::fromJson(
            json_encode(array(
                'name' => 'SomeUnknownEvent',
                'payload' => array(),
            ))
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('SomeUnknownEvent can not be unserialised');

        $serialised->toEvent();
    }
}
