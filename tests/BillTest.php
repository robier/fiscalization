<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Company;
use Robier\Fiscalization\Oib;
use Robier\Fiscalization\Operator;
use Robier\Fiscalization\Refund;
use Robier\Fiscalization\Tax;

/**
 * @coversDefaultClass \Robier\Fiscalization\Bill
 * @covers \Robier\Fiscalization\Bill::__construct
 */
final class BillTest extends TestCase
{
    private Bill $test;

    public function setUp(): void
    {
        $oib = new Oib('33718192591');

        $this->test = new Bill(
            new Company($oib, true),
            new Operator($oib),
            new DateTimeImmutable(),
            new Bill\Identifier(1, 'POS1', 1),
            Bill\PaymentType::cash(),
            Bill\SequenceType::shop(),
        );
    }

    /**
     * @covers ::identifier
     */
    public function testIdentifier(): void
    {
        self::assertInstanceOf(Bill\Identifier::class, $this->test->identifier());
    }

    /**
     * @covers ::billSequenceType
     */
    public function testBillSequenceType(): void
    {
        self::assertInstanceOf(Bill\SequenceType::class, $this->test->billSequenceType());
    }

    /**
     * @covers ::company
     */
    public function testCompany(): void
    {
        self::assertInstanceOf(Company::class, $this->test->company());
    }

    /**
     * @covers ::createdAt
     */
    public function testCreatedAt(): void
    {
        self::assertInstanceOf(DateTimeImmutable::class, $this->test->createdAt());
    }

    /**
     * @covers ::paymentType
     */
    public function testPaymentType(): void
    {
        self::assertInstanceOf(Bill\PaymentType::class, $this->test->paymentType());
    }

    /**
     * @covers ::operator
     */
    public function testOperator(): void
    {
        self::assertInstanceOf(Operator::class, $this->test->operator());
    }

    /**
     * @covers ::hasMarginTaxAmount
     */
    public function testHasMarginTaxAmount(): void
    {
        self::assertFalse($this->test->hasMarginTaxAmount());

        $this->test->setMarginTaxAmount(100_00);
        self::assertTrue($this->test->hasMarginTaxAmount());

        $this->test->removeMarginTaxAmount();
        self::assertFalse($this->test->hasMarginTaxAmount());
    }

    /**
     * @covers ::setMarginTaxAmount
     */
    public function testSetMarginTaxAmount(): void
    {
        self::assertNull($this->test->marginTaxAmount());
        $this->test->setMarginTaxAmount(100_00);
        self::assertSame(100_00, $this->test->marginTaxAmount());

        $this->test->setMarginTaxAmount(300_00);
        self::assertSame(300_00, $this->test->marginTaxAmount());
    }

    /**
     * @covers ::hasTaxFreeAmount
     */
    public function testHasTaxFreeAmount(): void
    {
        self::assertFalse($this->test->hasTaxFreeAmount());

        $this->test->setTaxFreeAmount(100_00);
        self::assertTrue($this->test->hasTaxFreeAmount());

        $this->test->removeTaxFreeAmount();
        self::assertFalse($this->test->hasTaxFreeAmount());
    }

    /**
     * @covers ::hasSpecialPurpose
     */
    public function testHasSpecialPurpose(): void
    {
        self::assertFalse($this->test->hasSpecialPurpose());

        $this->test->setSpecialPurpose('special purpose');
        self::assertTrue($this->test->hasSpecialPurpose());

        $this->test->removeSpecialPurpose();
        self::assertFalse($this->test->hasSpecialPurpose());
    }

    /**
     * @covers ::removeSpecialPurpose
     */
    public function testRemoveSpecialPurpose(): void
    {
        self::assertFalse($this->test->hasSpecialPurpose());
        self::assertNull($this->test->specialPurpose());

        $this->test->setSpecialPurpose('special purpose');
        self::assertTrue($this->test->hasSpecialPurpose());
        self::assertNotNull($this->test->specialPurpose());

        $this->test->removeSpecialPurpose();
        self::assertFalse($this->test->hasSpecialPurpose());
        self::assertNull($this->test->specialPurpose());
    }

    /**
     * @covers ::taxFreeAmount
     */
    public function testTaxFreeAmount(): void
    {
        self::assertNull($this->test->taxFreeAmount());

        $this->test->setTaxFreeAmount(100_00);
        self::assertSame(100_00, $this->test->taxFreeAmount());

        $this->test->removeTaxFreeAmount();
        self::assertNull($this->test->taxFreeAmount());
    }

    /**
     * @covers ::specialPurpose
     */
    public function testSpecialPurpose(): void
    {
        self::assertNull($this->test->specialPurpose());

        $this->test->setSpecialPurpose('special purpose');
        self::assertSame('special purpose', $this->test->specialPurpose());

        $this->test->removeSpecialPurpose();
        self::assertNull($this->test->specialPurpose());
    }

    /**
     * @covers ::hasParagonNumber
     */
    public function testHasParagonNumber(): void
    {
        self::assertFalse($this->test->hasParagonNumber());

        $this->test->setParagonNumber('12ab123');
        self::assertTrue($this->test->hasParagonNumber());

        $this->test->removeParagonNumber();
        self::assertFalse($this->test->hasParagonNumber());
    }

    /**
     * @covers ::taxes
     */
    public function testTaxes(): void
    {
        self::assertSame(0, $this->test->totalAmount());
        self::assertEmpty($this->test->taxes());

        $this->test->addTax(new Tax\Vat(100_00, 20_00, 20_00));
        self::assertSame(120_00, $this->test->totalAmount());
        self::assertNotEmpty($this->test->taxes());

        $this->test->removeTax();
        self::assertSame(0, $this->test->totalAmount());
        self::assertFalse($this->test->hasRefunds());
        self::assertEmpty($this->test->refunds());
    }

    /**
     * @covers ::removeTax
     */
    public function testRemoveTax(): void
    {
        self::assertFalse($this->test->hasTax());
        self::assertEmpty($this->test->taxes());

        $this->test->addTax(new Tax\Vat(100_00, 20_00));
        self::assertTrue($this->test->hasTax());
        self::assertNotEmpty($this->test->taxes());

        $this->test->removeTax();
        self::assertFalse($this->test->hasTax());
        self::assertEmpty($this->test->taxes());
    }

    /**
     * @covers ::hasTax
     */
    public function testHasTax(): void
    {
        self::assertFalse($this->test->hasTax());

        $this->test->addTax(new Tax\Vat(100_00, 20_00));
        self::assertTrue($this->test->hasTax());

        $this->test->removeTax();
        self::assertFalse($this->test->hasTax());
    }

    /**
     * @covers ::setTaxFreeAmount
     */
    public function testSetTaxFreeAmount(): void
    {
        self::assertNull($this->test->taxFreeAmount());
        $this->test->setTaxFreeAmount(100_00);
        self::assertSame(100_00, $this->test->taxFreeAmount());

        $this->test->setTaxFreeAmount(200_00);
        self::assertSame(200_00, $this->test->taxFreeAmount());
    }

    /**
     * @covers ::removeMarginTaxAmount
     */
    public function testRemoveMarginTaxAmount(): void
    {
        self::assertFalse($this->test->hasMarginTaxAmount());
        self::assertNull($this->test->marginTaxAmount());

        $this->test->setMarginTaxAmount(100_00);
        self::assertTrue($this->test->hasMarginTaxAmount());
        self::assertNotNull($this->test->marginTaxAmount());

        $this->test->removeMarginTaxAmount();
        self::assertFalse($this->test->hasMarginTaxAmount());
        self::assertNull($this->test->marginTaxAmount());
    }

    /**
     * @covers ::paragonNumber
     */
    public function testParagonNumber(): void
    {
        self::assertNull($this->test->paragonNumber());
        $this->test->setParagonNumber('12ab123');
        self::assertSame('12ab123', $this->test->paragonNumber());
        $this->test->removeParagonNumber();
        self::assertNull($this->test->paragonNumber());
    }

    /**
     * @covers ::hasRefunds
     */
    public function testHasRefunds(): void
    {
        self::assertFalse($this->test->hasRefunds());

        $this->test->addRefund(new Refund('test refund', 100_00));
        self::assertTrue($this->test->hasRefunds());

        $this->test->removeRefunds();
        self::assertFalse($this->test->hasRefunds());
    }

    /**
     * @covers ::removeRefunds
     */
    public function testRemoveRefunds(): void
    {
        self::assertSame(0, $this->test->totalAmount());
        self::assertFalse($this->test->hasRefunds());
        self::assertEmpty($this->test->refunds());

        $this->test->addRefund(new Refund('test refund', 100_00));
        self::assertSame(-100_00, $this->test->totalAmount());
        self::assertTrue($this->test->hasRefunds());
        self::assertNotEmpty($this->test->refunds());

        $this->test->removeRefunds();
        self::assertSame(0, $this->test->totalAmount());
        self::assertFalse($this->test->hasRefunds());
        self::assertEmpty($this->test->refunds());
    }

    /**
     * @covers ::removeTaxFreeAmount
     */
    public function testRemoveTaxFreeAmount(): void
    {
        self::assertFalse($this->test->hasTaxFreeAmount());
        self::assertNull($this->test->taxFreeAmount());

        $this->test->setTaxFreeAmount(100_00);
        self::assertTrue($this->test->hasTaxFreeAmount());
        self::assertNotNull($this->test->taxFreeAmount());

        $this->test->removeTaxFreeAmount();
        self::assertFalse($this->test->hasTaxFreeAmount());
        self::assertNull($this->test->taxFreeAmount());
    }

    /**
     * @covers ::removeParagonNumber
     */
    public function testRemoveParagonNumber(): void
    {
        self::assertFalse($this->test->hasParagonNumber());
        self::assertNull($this->test->paragonNumber());

        $this->test->setParagonNumber('test');
        self::assertTrue($this->test->hasParagonNumber());
        self::assertNotNull($this->test->paragonNumber());

        $this->test->removeParagonNumber();
        self::assertFalse($this->test->hasParagonNumber());
        self::assertNull($this->test->paragonNumber());
    }

    /**
     * @covers ::marginTaxAmount
     */
    public function testMarginTaxAmount(): void
    {
        self::assertNull($this->test->marginTaxAmount());
        $this->test->setMarginTaxAmount(100_00);
        self::assertSame(100_00, $this->test->marginTaxAmount());
        $this->test->removeMarginTaxAmount();
        self::assertNull($this->test->marginTaxAmount());
    }

    /**
     * @covers ::redelivery
     */
    public function testRedelivery(): void
    {
        // default value is false
        self::assertFalse($this->test->redelivery());

        $oib = new Oib('33718192591');

        $test = new Bill(
            new Company($oib, true),
            new Operator($oib),
            new DateTimeImmutable(),
            new Bill\Identifier(1, 'POS1', 1),
            Bill\PaymentType::cash(),
            Bill\SequenceType::shop(),
            true
        );

        self::assertTrue($test->redelivery());
    }

    /**
     * @covers ::setSpecialPurpose
     */
    public function testSetSpecialPurpose(): void
    {
        self::assertNull($this->test->specialPurpose());
        $this->test->setSpecialPurpose('special purpose');
        self::assertSame('special purpose', $this->test->specialPurpose());
    }

    /**
     * @covers ::setParagonNumber
     */
    public function testSetParagonNumber(): void
    {
        self::assertNull($this->test->paragonNumber());
        $this->test->setParagonNumber('12ab123');
        self::assertSame('12ab123', $this->test->paragonNumber());
    }

    public function addRefundDataProvider(): Generator
    {
        yield 'Single refund' => [
            [
                new Refund('test refund 1', 100_00),
            ],
            -100_00,
        ];

        yield 'Few refunds' => [
            [
                new Refund('test refund 1', 100_00),
                new Refund('test refund 2', 200_00),
                new Refund('test refund 3', 300_00),
            ],
            -600_00,
        ];
    }

    /**
     * @covers ::addRefund
     * @dataProvider addRefundDataProvider
     */
    public function testAddRefund(array $refunds, int $total): void
    {
        self::assertEmpty($this->test->refunds());

        $this->test->addRefund(...$refunds);

        self::assertCount(count($refunds), $this->test->refunds());
        self::assertSame($total, $this->test->totalAmount());
    }

    public function addTaxDataProvider(): Generator
    {
        yield 'Single VAT TAX' => [
            [
                new Tax\Vat(100_00, 10_00)
            ],
            110_00,
        ];

        yield 'Single consumption TAX' => [
            [
                new Tax\ConsumptionTax(200_00, 10_00)
            ],
            220_00,
        ];

        yield 'Single other TAX' => [
            [
                new Tax\Other('Test TAX', 300_00, 10_00)
            ],
            330_00,
        ];

        yield 'TAX combination' => [
            [
                new Tax\Vat(100_00, 10_00),
                new Tax\ConsumptionTax(200_00, 10_00),
                new Tax\Other('Test TAX', 300_00, 10_00)
            ],
            660_00,
        ];
    }

    /**
     * @covers ::addTax
     * @dataProvider addTaxDataProvider
     */
    public function testAddTax(array $taxes, int $total): void
    {
        self::assertEmpty($this->test->taxes());
        self::assertSame(0, $this->test->totalAmount());

        $this->test->addTax(...$taxes);

        self::assertCount(count($taxes), $this->test->taxes());
        self::assertSame($total, $this->test->totalAmount());
    }

    public function refundsDataProvider(): Generator
    {
        yield 'Single refund' => [
            [
                new Refund('test refund 1', 100_00),
            ],
            -100_00,
        ];

        yield 'Multiple refund' => [
            [
                new Refund('test refund 1', 100_00),
                new Refund('test refund 2', 200_00),
                new Refund('test refund 3', 300_00),
            ],
            -600_00,
        ];
    }

    /**
     * @covers ::refunds
     * @dataProvider refundsDataProvider
     */
    public function testRefunds(array $refunds, int $total): void
    {
        self::assertEmpty($this->test->refunds());
        self::assertSame(0, $this->test->totalAmount());

        $this->test->addRefund(...$refunds);

        self::assertCount(count($refunds), $this->test->refunds());
        self::assertSame($total, $this->test->totalAmount());
    }

    public function totalAmountDataProvider(): Generator
    {
        yield 'Only taxes and refunds' => [
            [
                'tax' => [
                    new Tax\Vat(100_00, 20_00),
                    new Tax\Other('Other TAX 1', 100_00, 25_00),
                ],
                'refund' => [
                    new Refund('Refund 1', 10_00),
                    new Refund('Refund 2', 20_00),
                ]
            ],
            215_00
        ];

        yield 'All data' => [
            [
                'tax' => [
                    new Tax\Vat(100_00, 20_00),
                ],
                'refund' => [
                    new Refund('Refund 1', 10_00),
                ],
                'taxFreeAmount' => 20_00,
                'marginTaxAmount' => 10_00,
            ],
            140_00
        ];
    }

    /**
     * @covers ::totalAmount
     * @dataProvider totalAmountDataProvider
     */
    public function testTotalAmount(array $data, int $total): void
    {
        self::assertSame(0, $this->test->totalAmount());

        if (isset($data['tax'])) {
            $this->test->addTax(...$data['tax']);
        }

        if (isset($data['refund'])) {
            $this->test->addRefund(...$data['refund']);
        }

        if (isset($data['taxFreeAmount'])) {
            $this->test->setTaxFreeAmount($data['taxFreeAmount']);
        }

        if (isset($data['marginTaxAmount'])) {
            $this->test->setMarginTaxAmount($data['marginTaxAmount']);
        }

        self::assertSame($total, $this->test->totalAmount());
    }
}
