<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

final class Operator
{
    public function __construct(private Oib $oib)
    {
        // noop
    }

    public function oib(): Oib
    {
        return $this->oib;
    }
}
