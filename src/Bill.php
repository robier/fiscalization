<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

use DateTimeImmutable;

final class Bill
{
    private ?int $taxFreeAmount = null;
    private int $totalAmount = 0;
    private ?int $marginTaxAmount = null;

    private array $taxes = [];
    private array $refunds = [];

    private ?string $paragonNumber = null;

    private ?string $specialPurpose = null;

    public function __construct(
        private Company $company,
        private Operator $operator,
        private DateTimeImmutable $createdAt,
        private Bill\Identifier $identifier,
        private Bill\PaymentType $paymentType,
        private Bill\SequenceType $billSequenceType,
        private bool $redelivery = false
    ) {
        // noop
    }

    public function addTax(Tax $tax, Tax ...$taxes): self
    {
        array_unshift($taxes, $tax);

        foreach ($taxes as $tax) {
            $this->totalAmount += $tax->sum();
            $this->taxes[] = $tax;
        }

        return $this;
    }

    public function addRefund(Refund $refund, Refund ...$refunds): self
    {
        array_unshift($refunds, $refund);

        foreach ($refunds as $refund) {
            $this->totalAmount -= $refund->value();
            $this->refunds[] = $refund;
        }

        return $this;
    }

    /** @return Tax[] */
    public function taxes(): array
    {
        return $this->taxes;
    }

    /** @return Refund[] */
    public function refunds(): array
    {
        return $this->refunds;
    }

    public function hasRefunds(): bool
    {
        return empty($this->refunds) === false;
    }

    public function company(): Company
    {
        return $this->company;
    }

    public function operator(): Operator
    {
        return $this->operator;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setTaxFreeAmount(int $value): self
    {
        if ($this->taxFreeAmount !== null) {
            // reduce previous set value
            $this->totalAmount -= $this->taxFreeAmount;
        }

        $this->totalAmount += $value;
        $this->taxFreeAmount = $value;

        return $this;
    }

    public function removeTaxFreeAmount(): self
    {
        $this->totalAmount -= $this->taxFreeAmount;
        $this->taxFreeAmount = null;

        return $this;
    }

    public function hasTaxFreeAmount(): bool
    {
        return $this->taxFreeAmount !== null;
    }

    public function taxFreeAmount(): ?int
    {
        return $this->taxFreeAmount;
    }

    public function setMarginTaxAmount(int $value): self
    {
        if ($this->marginTaxAmount !== null) {
            // reduce previous set value
            $this->totalAmount -= $this->marginTaxAmount;
        }

        $this->totalAmount += $value;
        $this->marginTaxAmount = $value;

        return $this;
    }

    public function removeMarginTaxAmount(): self
    {
        $this->totalAmount -= $this->marginTaxAmount;
        $this->marginTaxAmount = null;

        return $this;
    }

    public function hasMarginTaxAmount(): bool
    {
        return $this->marginTaxAmount !== null;
    }

    public function marginTaxAmount(): ?int
    {
        return $this->marginTaxAmount;
    }

    public function identifier(): Bill\Identifier
    {
        return $this->identifier;
    }

    public function totalAmount(): int
    {
        return $this->totalAmount;
    }

    public function paymentType(): Bill\PaymentType
    {
        return $this->paymentType;
    }

    public function billSequenceType(): Bill\SequenceType
    {
        return $this->billSequenceType;
    }

    public function hasParagonNumber(): bool
    {
        return $this->paragonNumber !== null;
    }

    public function setParagonNumber(string $paragon): self
    {
        $this->paragonNumber = $paragon;

        return $this;
    }

    public function removeParagonNumber(): self
    {
        $this->paragonNumber = null;

        return $this;
    }

    public function paragonNumber(): ?string
    {
        return $this->paragonNumber;
    }

    public function redelivery(): bool
    {
        return $this->redelivery;
    }

    public function setSpecialPurpose(string $label): self
    {
        $this->specialPurpose = $label;

        return $this;
    }

    public function hasSpecialPurpose(): bool
    {
        return $this->specialPurpose !== null;
    }

    public function removeSpecialPurpose(): self
    {
        $this->specialPurpose = null;

        return $this;
    }

    public function specialPurpose(): ?string
    {
        return $this->specialPurpose;
    }

    public function removeTax(): self
    {
        foreach ($this->taxes as $tax) {
            $this->totalAmount -= $tax->sum();
        }

        $this->taxes = [];

        return $this;
    }

    public function hasTax(): bool
    {
        return empty($this->taxes) === false;
    }

    public function removeRefunds(): self
    {
        foreach ($this->refunds as $refund) {
            $this->totalAmount += $refund->value();
        }

        $this->refunds = [];

        return $this;
    }
}
