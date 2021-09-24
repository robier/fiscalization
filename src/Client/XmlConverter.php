<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

use DateTimeImmutable;
use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Tax\ConsumptionTax;
use Robier\Fiscalization\Tax\Other;
use Robier\Fiscalization\Xml\Element;

/**
 * @internal
 */
final class XmlConverter
{
    private const DATE_TIME_FORMAT = 'd.m.Y\TH:i:s';

    public function encode(Bill $billInvoice, string $securityCode): array
    {
        $randomUuid = $this->randomUuid();

        $header = (new Element('tns:Zaglavlje'))
            ->addChild((new Element('tns:IdPoruke'))
                ->addChild($randomUuid)
            )
            ->addChild((new Element('tns:DatumVrijeme'))
                ->addChild((new DateTimeImmutable())->format(self::DATE_TIME_FORMAT))
            );

        $billElements = new Element('tns:Racun');

        $billElements
            ->addChild((new Element('tns:Oib'))->addChild($billInvoice->company()->oib()->value()))
            ->addChild((new Element('tns:USustPdv'))->addChild($billInvoice->company()->insideVatRegistry() ? 1 : 0))
            ->addChild((new Element('tns:DatVrijeme'))
                ->addChild(
                    $billInvoice->createdAt()->format(self::DATE_TIME_FORMAT)
                ))
            ->addChild((new Element('tns:OznSlijed'))->addChild($billInvoice->billSequenceType()->type()))
            ->addChild((new Element('tns:BrRac'))
                ->addChild((new Element('tns:BrOznRac'))->addChild($billInvoice->identifier()->billNumber()))
                ->addChild((new Element('tns:OznPosPr'))->addChild($billInvoice->identifier()->storeDesignation()))
                ->addChild((new Element('tns:OznNapUr'))->addChild($billInvoice->identifier()->tollDeviceNumber()))
            );

        $taxElements = $this->setupTax($billInvoice);
        foreach (['vat' => 'tns:Pdv', 'consumptionTax' => 'tns:Pnp', 'other' => 'tns:OstaliPor'] as $taxTypeName => $taxTypeElement) {
            if (isset($taxElements[$taxTypeName])) {
                $billElements->addChild((new Element($taxTypeElement))->addChild(...$taxElements[$taxTypeName]));
            }
        }

        if ($billInvoice->hasTaxFreeAmount()) {
            $billElements->addChild((new Element('tns:IznosOslobPdv'))->addChild(sprintf('%.2f', $billInvoice->taxFreeAmount() / 100.0)));
        }

        if ($billInvoice->hasMarginTaxAmount()) {
            $billElements->addChild((new Element('tns:IznosMarza'))->addChild(sprintf('%.2f', $billInvoice->marginTaxAmount() / 100.0)));
        }

        if ($billInvoice->hasRefunds()) {
            $billElements->addChild((new Element('tns:Naknade'))->addChild(...$this->setupRepayments($billInvoice)));
        }

        $billElements
            ->addChild((new Element('tns:IznosUkupno'))->addChild(sprintf('%.2f', $billInvoice->totalAmount() / 100.0)))
            ->addChild((new Element('tns:NacinPlac'))->addChild($billInvoice->paymentType()->type()))
            ->addChild((new Element('tns:OibOper'))->addChild($billInvoice->operator()->oib()->value()))
            ->addChild((new Element('tns:ZastKod'))->addChild($securityCode))
            ->addChild((new Element('tns:NakDost'))
                ->addChild($billInvoice->redelivery() ? 1 : 0)
            );

        if ($billInvoice->hasParagonNumber()) {
            $billElements->addChild((new Element('tns:ParagonBrRac'))->addChild($billInvoice->paragonNumber()));
        }

        if ($billInvoice->hasSpecialPurpose()) {
            $billElements->addChild((new Element('tns:SpecNamj'))->addChild($billInvoice->specialPurpose()));
        }

        return [
            $header,
            $billElements
        ];
    }

    /**
     * @return Element[]
     */
    private function setupTax(Bill $billInvoice): array
    {
        $taxesElements = [];

        foreach ($billInvoice->taxes() as $tax) {
            $taxType = 'vat';
            if ($tax instanceof ConsumptionTax) {
                $taxType = 'consumptionTax';
            }
            if ($tax instanceof Other) {
                $taxType = 'other';
            }

            $taxElement = new Element('tns:Porez');

            if ($tax instanceof Other) {
                $taxElement->addChild((new Element('tns:Naziv'))->addChild($tax->label()));
            }

            $taxElement
                ->addChild((new Element('tns:Stopa'))->addChild(sprintf('%.2f', $tax->rate() / 100.0)))
                ->addChild((new Element('tns:Osnovica'))->addChild(sprintf('%.2f', $tax->base() / 100.0)))
                ->addChild((new Element('tns:Iznos'))->addChild(sprintf('%.2f', $tax->amount() / 100.0)));

            $taxesElements[$taxType][] = $taxElement;
        }

        return $taxesElements;
    }

    /**
     * @return Element[]
     */
    private function setupRepayments(Bill $billInvoice): array
    {
        if ($billInvoice->hasRefunds() === false) {
            return [];
        }

        $repayments = [];

        foreach ($billInvoice->refunds() as $repayment) {
            $repayments[] = (new Element('tns:Naknada'))
                ->addChild((new Element('tns:NazivN'))->addChild($repayment->name()))
                ->addChild((new Element('tns:IznosN'))->addChild(sprintf('%.2f', $repayment->value() / 100.0)));
        }

        return $repayments;
    }

    private function randomUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
