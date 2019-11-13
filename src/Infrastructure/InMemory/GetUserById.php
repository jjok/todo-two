<?php declare(strict_types=1);

namespace jjok\TodoTwo\Infrastructure\InMemory;

use jjok\TodoTwo\Domain\User;
use jjok\TodoTwo\Domain\User\Id;

final class GetUserById implements \jjok\TodoTwo\Domain\User\Query\GetUserById
{
    public function __construct(User ...$users)
//    public function __construct(array $userData)
    {
//        $users = [];
//
//        foreach($userData as $id => $name) {
//            $users[] = new User(Id::fromString($id), $name);
//        }

        $this->users = $users;
    }

    private $users;

    public function execute(Id $id): User
    {
        /** @var User $user */
        foreach ($this->users as $user) {
            if($user->id()->toString() === $id->toString()) {
                return $user;
            }
        }

        throw User\NotFound::fromId($id);
    }
}
