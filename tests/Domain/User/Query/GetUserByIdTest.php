<?php

namespace jjok\TodoTwo\Domain\User\Query;

use jjok\TodoTwo\Domain\User;
use PHPUnit\Framework\TestCase;

abstract class GetUserByIdTest extends TestCase
{
    abstract protected function getQuery(User ...$users) : GetUserById;

    /**
     * @test
     * @testWith ["4f0568b1-6337-4370-9800-4f1737cb501f", "User 1"]
     *           ["11078104-893d-40db-a2ba-aee08bc1cacc", "User 2"]
     */
    public function a_registered_user_can_be_found(string $id, string $name) : void
    {
        $getUserById = $this->getQuery(
            new User(User\Id::fromString($id), $name)
        );

        $user = $getUserById->execute(User\Id::fromString($id));

        $this->assertSame($id, $user->id()->toString());
        $this->assertSame($name, $user->name());
    }

    /**
     * @test
     * @testWith ["4f0568b1-6337-4370-9800-4f1737cb501f"]
     *           ["11078104-893d-40db-a2ba-aee08bc1cacc"]
     */
    public function an_unregistered_user_can_not_be_found(string $id) : void
    {
        $getUserById = $this->getQuery(
            new User(User\Id::fromString('4f0568b1-6337-4370-9800-4f1737cb501a'), 'Some other user')
        );

        $this->expectException(User\NotFound::class);
        $this->expectExceptionMessage('There is no registered user with the ID');

        $getUserById->execute(User\Id::fromString($id));
    }
}
