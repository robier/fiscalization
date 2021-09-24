<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Tax;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Tax\Vat;

/**
 * @coversDefaultClass \Robier\Fiscalization\Tax\Vat
 * @covers \Robier\Fiscalization\Tax\Vat::__construct
 * @covers \Robier\Fiscalization\Tax\Base
 */
final class VatTest extends TestCase
{
    private Vat $test;

    protected function setUp(): void
    {
        $this->test = new Vat(20_00, 30_00, 6_00);
    }

    /**
     * @covers ::sum
     */
    public function testSum(): void
    {
        self::assertSame(26_00, $this->test->sum());
    }

    /**
     * @covers ::rate
     */
    public function testRate(): void
    {
        self::assertSame(30_00, $this->test->rate());
    }

    /**
     * @covers ::amount
     */
    public function testAmount(): void
    {
        self::assertSame(6_00, $this->test->amount());
    }

    /**
     * @covers ::base
     */
    public function testBase(): void
    {
        self::assertSame(20_00, $this->test->base());
    }
}
