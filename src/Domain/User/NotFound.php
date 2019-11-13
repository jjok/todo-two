<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\User;

use Exception;

final class NotFound extends Exception
{
    public static function fromId(Id $id) : self
    {
        return new self(sprintf('There is no registered user with the ID "%s".', $id->toString()));
    }
}
