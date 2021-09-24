<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Oib;

use Generator;
use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Oib\Validator;

/**
 * @coversDefaultClass \Robier\Fiscalization\Oib\Validator
 */
final class ValidatorTest extends TestCase
{
    public function validDataProvider(): Generator
    {
        yield 'Valid OIB' => [
            true,
            '75539867617'
        ];

        yield 'Valid OIB with last 0' => [
            true,
            '79930628140'
        ];

        yield 'Invalid OIB data' => [
            false,
            '00000000000'
        ];

        yield 'Invalid OIB length' => [
            false,
            '000000'
        ];
    }

    /**
     * @covers ::valid
     * @dataProvider validDataProvider
     */
    public function testValid(bool $valid, string $data): void
    {
        self::assertSame($valid, (new Validator())->valid($data));
    }
}
