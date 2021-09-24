<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Bill;

use Robier\Fiscalization\Exception\InvalidArgument;

final class SequenceType
{
    private const POSSIBLE_VALUES = [
        'P', // shop
        'N', // billing device
    ];

    public function __construct(private string $type)
    {
        if (!in_array($this->type, self::POSSIBLE_VALUES, true)) {
            new InvalidArgument('Wrong bill sequence type provided');
        }
    }

    public static function shop(): self
    {
        return new static('P');
    }

    public static function billingDevice(): self
    {
        return new static('N');
    }

    public function type(): string
    {
        return $this->type;
    }

    public function equal(self $billSequenceType): bool
    {
        return $this->type === $billSequenceType->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
