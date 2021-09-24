<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Bill;

final class Identifier
{
    public function __construct(
        private int $billNumber,
        private string $storeDesignation,
        private int $tollDeviceNumber
    ) {
        // noop
    }

    public function billNumber(): int
    {
        return $this->billNumber;
    }

    public function storeDesignation(): string
    {
        return $this->storeDesignation;
    }

    public function tollDeviceNumber(): int
    {
        return $this->tollDeviceNumber;
    }
}
