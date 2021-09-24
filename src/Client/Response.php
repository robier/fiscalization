<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

/**
 * @internal
 */
final class Response
{
    public function __construct(private string $uniqueBillIdentifier, private string $issuerSecurityCode)
    {
        // noop
    }

    /**
     * Local name "JIR - Jedinstveni identifikator računa"
     */
    public function uniqueBillIdentifier(): string
    {
        return $this->uniqueBillIdentifier;
    }

    /**
     * Local name "ZKI - Zaštitni Kod Izdavatelja (računa)"
     */
    public function issuerSecurityCode(): string
    {
        return $this->issuerSecurityCode;
    }
}
