<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Refund;

/**
 * @coversDefaultClass \Robier\Fiscalization\Refund
 * @covers \Robier\Fiscalization\Refund::__construct
 */
final class RefundTest extends TestCase
{
    private Refund $test;

    public function setUp(): void
    {
        $this->test = new Refund('Test refund', 10_00);
    }

    /**
     * @covers ::name
     */
    public function testName(): void
    {
        self::assertSame('Test refund', $this->test->name());
    }

    /**
     * @covers ::value
     */
    public function testValue(): void
    {
        self::assertSame(10_00, $this->test->value());
    }
}
