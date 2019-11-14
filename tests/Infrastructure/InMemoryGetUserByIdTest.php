<?php

namespace jjok\TodoTwo\Infrastructure;

use jjok\TodoTwo\Domain\User;
use jjok\TodoTwo\Domain\User\Query\GetUserById;
use jjok\TodoTwo\Domain\User\Query\GetUserByIdTest;

final class InMemoryGetUserByIdTest extends GetUserByIdTest
{
    protected function getQuery(User ...$users): GetUserById
    {
        return new InMemory\GetUserById(...$users);
    }
}
