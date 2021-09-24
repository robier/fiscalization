<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Oib;

/**
 * @coversDefaultClass \Robier\Fiscalization\Oib
 * @covers \Robier\Fiscalization\Oib::__construct
 */
final class OibTest extends TestCase
{
    private Oib $test;

    public function setUp(): void
    {
        $this->test = new Oib('67173949523');
    }

    /**
     * @covers ::__construct
     */
    public function testOibValidation(): void
    {
        self::expectException(InvalidArgument::class);

        new Oib('0000000000');
    }

    /**
     * @covers ::value
     */
    public function testValue(): void
    {
        self::assertSame('67173949523', $this->test->value());
    }

    /**
     * @covers ::__toString
     */
    public function test__toString(): void
    {
        self::assertSame('67173949523', $this->test->__toString());
        self::assertSame('67173949523', (string)$this->test);
    }
}
