<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain;

use jjok\TodoTwo\Domain\User\Id;

final class User
{
    public function __construct(Id $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    private Id $id;
    private string $name;

    public function id() : Id
    {
        return $this->id;
    }

    public function name() : string
    {
        return $this->name;
    }
}
