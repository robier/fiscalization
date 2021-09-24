<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Client;

use Generator;
use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Client\SoapEnvelope;
use Robier\Fiscalization\Xml\Element;

/**
 * @coversDefaultClass \Robier\Fiscalization\Client\SoapEnvelope
 * @covers \Robier\Fiscalization\Client\SoapEnvelope::__construct
 */
final class SoapEnvelopeTest extends TestCase
{
    public function stringDataProvider(): Generator
    {
        yield 'String body' => [
            'foobar'
        ];

        yield 'Simple XML body' => [
            '<foobar/>'
        ];

        yield 'Complex XML body' => [
            '<foobar test="demo">test</foobar>'
        ];
    }

    /**
     * @covers ::__toString
     * @dataProvider stringDataProvider
     */
    public function testCreatingFromString(string $data): void
    {
        $test = new SoapEnvelope($data);

        $envelope = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body>$data</soapenv:Body></soapenv:Envelope>
        
        XML;


        self::assertSame($envelope, $test->__toString());
        self::assertSame($envelope, (string)$test);
    }

    /**
     * @covers ::fromElements
     */
    public function testCreatingFromElements(): void
    {
        $test = SoapEnvelope::fromElements(new Element('test'));

        $envelope = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body><test/></soapenv:Body></soapenv:Envelope>
        
        XML;

        self::assertSame($envelope, $test->__toString());
        self::assertSame($envelope, (string)$test);
    }
}
