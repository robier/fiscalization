<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Bill;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Bill\Identifier;

/**
 * @coversDefaultClass \Robier\Fiscalization\Bill\Identifier
 * @covers \Robier\Fiscalization\Bill\Identifier::__construct
 */
final class IdentifierTest extends TestCase
{
    private Identifier $test;

    protected function setUp(): void
    {
        $this->test = new Identifier(
            1,
            'POS1',
            2
        );
    }

    /**
     * @covers ::billNumber
     */
    public function testBillNumber(): void
    {
        self::assertSame(1, $this->test->billNumber());
    }

    /**
     * @covers ::tollDeviceNumber
     */
    public function testTollDeviceNumber(): void
    {
        self::assertSame(2, $this->test->tollDeviceNumber());
    }

    /**
     * @covers ::storeDesignation
     */
    public function testStoreDesignation(): void
    {
        self::assertSame('POS1', $this->test->storeDesignation());
    }
}
