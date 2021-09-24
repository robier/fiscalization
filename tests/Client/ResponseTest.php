<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Client;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Client\Response;

/**
 * @coversDefaultClass \Robier\Fiscalization\Client\Response
 * @covers \Robier\Fiscalization\Client\Response::__construct
 */
final class ResponseTest extends TestCase
{
    private Response $test;

    protected function setUp(): void
    {
        $this->test = new Response(
            'bill-identifier',
            'security-code',
        );
    }

    /**
     * @covers ::uniqueBillIdentifier
     */
    public function testUniqueBillIdentifier(): void
    {
        self::assertSame('bill-identifier', $this->test->uniqueBillIdentifier());
    }

    /**
     * @covers ::issuerSecurityCode
     */
    public function testIssuerSecurityCode(): void
    {
        self::assertSame('security-code', $this->test->issuerSecurityCode());
    }
}
