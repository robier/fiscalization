<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Certificate;
use Robier\Fiscalization\Company;
use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Oib;
use Robier\Fiscalization\Operator;
use Robier\Fiscalization\Xml\Element;

/**
 * @coversDefaultClass \Robier\Fiscalization\Certificate
 * @covers \Robier\Fiscalization\Certificate::__construct
 * @covers \Robier\Fiscalization\Certificate::canonicalizeXml
 * @covers \Robier\Fiscalization\Certificate::sign
 */
final class CertificateTest extends TestCase
{
    private Certificate $test;

    protected function setUp(): void
    {
        $this->test = new Certificate(
            __DIR__ . '/resources/root.crt',
            __DIR__ . '/resources/cert.p12',
            '********'
        );
    }

    /**
     * @covers ::generateSecurityCode
     */
    public function testGenerateSecurityCode(): void
    {
        $oib = new Oib('79646268126');
        $bill = new Bill(
            new Company($oib, true),
            new Operator($oib),
            new DateTimeImmutable(),
            new Bill\Identifier(1, 'POS1', 1),
            Bill\PaymentType::cash(),
            Bill\SequenceType::shop(),
            true
        );

        $code = $this->test->generateSecurityCode($bill);

        self::assertSame(32, strlen($code));
    }

    /**
     * @covers ::signXml
     */
    public function testSignXml(): void
    {
        $element = new Element('test', ['Id' => 'testID']);

        self::assertEmpty($element->children());
        $this->test->signXml($element);

        self::assertNotEmpty($element->children());
    }

    /**
     * @covers ::signXml
     */
    public function testSignXmlThrowingException(): void
    {
        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('Element provided is missing "Id" attribute');
        $element = new Element('test');

        $this->test->signXml($element);
    }

    /**
     * @covers ::rootCertificatePath
     */
    public function testRootCertificatePath(): void
    {
        self::assertSame(__DIR__ . '/resources/root.crt', $this->test->rootCertificatePath());
    }

    public function constructorThrowingExceptionDataProvider()
    {
        yield 'Root certificate does not exists' => [
            [
                'not_exiting_root_certificate',
                __DIR__ . '/resources/cert.p12',
                '********',
            ],
            'Root certificate on path not_exiting_root_certificate not found or not readable',
        ];

        yield 'Certificate does not exist' => [
            [
                __DIR__ . '/resources/root.crt',
                'not_existing_certificate',
                '********',
            ],
            'Certificate on path not_existing_certificate not found',
        ];

        yield 'Wrong passphrase' => [
            [
                __DIR__ . '/resources/root.crt',
                __DIR__ . '/resources/cert.p12',
                'bad passphrase',
            ],
            'Could not read certificate store, passphrase could be wrong',
        ];
    }

    /**
     * @covers ::__construct
     * @dataProvider constructorThrowingExceptionDataProvider
     */
    public function testConstructorTrowingException(array $params, string $message)
    {
        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage($message);

        new Certificate(...$params);
    }
}
