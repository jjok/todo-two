<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Id
{
//    /**
//     * @throws \Exception
//     */
//    public static function generate() : self
//    {
//        return new self(Uuid::uuid4());
//    }

    public static function fromString(string $uuid) : self
    {
        return new self(Uuid::fromString($uuid));
    }

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    private UuidInterface $uuid;

    public function toString() : string
    {
        return $this->uuid->toString();
    }
}
