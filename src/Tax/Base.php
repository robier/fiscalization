<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Tax;

use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Tax;

abstract class Base implements Tax
{
    public function __construct(protected int $base, protected int $rate, protected ?int $amount = null)
    {
        $calculatedAmount = (int) ($this->base * ($this->rate / 10000));

        if ($this->amount === null) {
            $this->amount = $calculatedAmount;
            return;
        }

        if ($calculatedAmount !== $this->amount) {
            throw new InvalidArgument("Amount provided for tax base $base and rate $rate is wrong ($amount) $calculatedAmount");
        }
    }

    public function base(): int
    {
        return $this->base;
    }

    public function rate(): int
    {
        return $this->rate;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function sum(): int
    {
        return $this->amount + $this->base;
    }
}
