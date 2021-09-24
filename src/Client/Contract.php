<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

use Robier\Fiscalization\Bill;

interface Contract
{
    public function send(Bill $bill): Response;

    public function ping(): void;
}
