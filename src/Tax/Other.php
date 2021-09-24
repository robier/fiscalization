<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Tax;

final class Other extends Base
{
    public function __construct(protected string $label, int $base, int $rate, ?int $sum = null)
    {
        parent::__construct($base, $rate, $sum);
    }

    public function label(): string
    {
        return $this->label;
    }
}
