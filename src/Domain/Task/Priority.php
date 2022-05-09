<?php declare(strict_types=1);

namespace jjok\TodoTwo\Domain\Task;

final class Priority
{
    public static function fromInt(int $value) : self
    {
        return new self($value);
    }

    public function __construct(int $value)
    {
        if($value < 1 || $value > 100) {
            throw new \InvalidArgumentException(sprintf('Priority must be between 1 and 100. %u given.', $value));
        }

        $this->value = $value;
    }

    private int $value;

    public function toInt() : int
    {
        return $this->value;
    }
}
