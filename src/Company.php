<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

final class Company
{
    public function __construct(protected Oib $oib, protected bool $insideVatRegistry)
    {
        // noop
    }

    public function oib(): Oib
    {
        return $this->oib;
    }

    public function insideVatRegistry(): bool
    {
        return $this->insideVatRegistry;
    }
}
