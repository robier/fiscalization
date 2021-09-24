<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

/**
 * Local name "naknada"
 */
final class Refund
{
    public function __construct(private string $name, private int $value)
    {
        // noop
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): int
    {
        return $this->value;
    }
}
