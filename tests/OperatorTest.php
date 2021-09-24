<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Oib;
use Robier\Fiscalization\Operator;

/**
 * @coversDefaultClass \Robier\Fiscalization\Operator
 * @covers \Robier\Fiscalization\Operator::__construct
 */
final class OperatorTest extends TestCase
{
    private Operator $test;

    public function setUp(): void
    {
        $this->test = new Operator(new Oib('67173949523'));
    }

    /**
     * @covers ::oib
     */
    public function testOib(): void
    {
        self::assertInstanceOf(Oib::class, $this->test->oib());
        self::assertSame('67173949523', (string)$this->test->oib());
    }
}
