<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\User;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Id
{
    public static function fromString(string $uuid) : self
    {
        return new self(Uuid::fromString($uuid));
    }

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    private $uuid;

    public function toString() : string
    {
        return $this->uuid->toString();
    }
}
