<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Certificate;
use Robier\Fiscalization\Client;

/**
 * @coversDefaultClass \Robier\Fiscalization\Client
 */
final class ClientTest extends TestCase
{
    private Certificate $certificate;

    protected function setUp(): void
    {
        $this->certificate = new Certificate(
            __DIR__ . '/resources/root.crt',
            __DIR__ . '/resources/cert.p12',
            '********'
        );
    }

    /**
     * @covers ::demo
     */
    public function testDemo(): void
    {
        self::assertInstanceOf(Client\Demo::class, Client::demo($this->certificate));
    }

    /**
     * @covers ::production
     */
    public function testProduction(): void
    {
        self::assertInstanceOf(Client\Production::class, Client::production($this->certificate));
    }
}
