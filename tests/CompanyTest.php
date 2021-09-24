<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Company;
use Robier\Fiscalization\Oib;

/**
 * @coversDefaultClass \Robier\Fiscalization\Company
 * @covers \Robier\Fiscalization\Company::__construct
 */
final class CompanyTest extends TestCase
{
    private Company $test;

    public function setUp(): void
    {
        $this->test = new Company(new Oib('67173949523'), true);
    }

    /**
     * @covers ::insideVatRegistry
     */
    public function testInsideVatRegistry(): void
    {
        self::assertTrue($this->test->insideVatRegistry());
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
