<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\User\Query;

use jjok\TodoTwo\Domain\User;
use jjok\TodoTwo\Domain\User\Id;

interface GetUserById
{
    /**
     * @throws User\NotFound
     */
    public function execute(Id $id) : User;
}
