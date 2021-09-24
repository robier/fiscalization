<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Oib\Validator;

final class Oib
{
    public function __construct(private string $value)
    {
        $validator = new Validator();
        if ($validator->valid($this->value) === false) {
            throw new InvalidArgument('Invalid OIB number provided');
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
