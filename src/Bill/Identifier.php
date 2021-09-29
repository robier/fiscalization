<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Bill;

use Robier\Fiscalization\Exception\InvalidArgument;

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

    public function __toString(): string
    {
        return sprintf(
            '%d/%s/%d',
            $this->billNumber,
            $this->storeDesignation,
            $this->tollDeviceNumber
        );
    }

    public static function fromString(string $identifier): self
    {
        if (preg_match('/(\d+)\/(\w+)\/(\d+)/i', $identifier, $result) === 0) {
            throw new InvalidArgument('Provided identifier is not in good format');
        }

        return new self((int)$result[1], $result[2], (int)$result[3]);
    }

    public function next(): self
    {
        return new static($this->billNumber + 1, $this->storeDesignation, $this->tollDeviceNumber);
    }
}
