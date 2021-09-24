<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Bill;

use Robier\Fiscalization\Exception\InvalidArgument;

final class PaymentType
{
    private const POSSIBLE_VALUES = [
        'G', // cash
        'K', // cards
        'C', // check
        'T', // bank transfer
        'O', // other
    ];

    public function __construct(private string $type)
    {
        if (!in_array($this->type, self::POSSIBLE_VALUES, true)) {
            new InvalidArgument('Wrong payment type provided');
        }
    }

    public static function cash(): self
    {
        return new static('G');
    }

    public static function card(): self
    {
        return new static('K');
    }

    public static function check(): self
    {
        return new static('C');
    }

    public static function bankTransfer(): self
    {
        return new static('T');
    }

    public static function other(): self
    {
        return new static('O');
    }

    public function type(): string
    {
        return $this->type;
    }

    public function equal(self $paymentType): bool
    {
        return $this->type === $paymentType->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
