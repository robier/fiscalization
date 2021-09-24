<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Test\Xml;

use Generator;
use PHPUnit\Framework\TestCase;
use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Xml\Element;

/**
 * @coversDefaultClass \Robier\Fiscalization\Xml\Element
 * @covers \Robier\Fiscalization\Xml\Element::__construct
 */
final class ElementTest extends TestCase
{
    private Element $test;

    public function setUp(): void
    {
        $this->test = new Element(
            'test',
            [
                'foo' => 'bar'
            ]
        );
    }

    /**
     * @covers ::getChildByName
     */
    public function testGetChildByName(): void
    {
        $test = new Element('element4');

        $this->test->addChild(
            new Element('element1'),
            'text',
            new Element('element2'),
            new Element('element3'),
            $test,
            new Element('element5'),
        );

        self::assertSame($test, $this->test->getChildByName('element4'));
    }

    /**
     * @covers ::getChildByName
     */
    public function testGetChildByNameThrowException(): void
    {
        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('Child with name element1 does not exist');

        $this->test->getChildByName('element1');
    }

    public function toStringDataProvider(): Generator
    {
        yield 'Simple element' => [
            new Element('test'),
            '<test/>'
        ];

        yield 'Element with attributes' => [
            new Element('test', ['foo' => 'bar', 'bar' => 'foo']),
            '<test foo="bar" bar="foo"/>'
        ];

        yield 'Element with attributes and text child' => [
            (new Element('test', ['foo' => 'bar', 'bar' => 'foo']))->addChild('foo'),
            '<test foo="bar" bar="foo">foo</test>'
        ];

        yield 'Element with attributes and element child' => [
            (new Element('test', ['foo' => 'bar', 'bar' => 'foo']))->addChild(new Element('foo')),
            '<test foo="bar" bar="foo"><foo/></test>'
        ];

        yield 'Element with attributes and element child with text' => [
            (new Element('test', ['foo' => 'bar', 'bar' => 'foo']))->addChild((new Element('foo'))->addChild('test')),
            '<test foo="bar" bar="foo"><foo>test</foo></test>'
        ];

        yield 'Element with attributes and element child with text and attributes' => [
            (new Element('test', ['foo' => 'bar', 'bar' => 'foo']))->addChild((new Element('foo', ['test' => 'demo']))->addChild('test')),
            '<test foo="bar" bar="foo"><foo test="demo">test</foo></test>'
        ];
    }

    /**
     * @covers ::__toString
     * @dataProvider toStringDataProvider
     */
    public function testToString(Element $element, string $xml): void
    {
        self::assertSame($xml, (string)$element);
        self::assertSame($xml, $element->__toString());
    }

    /**
     * @covers ::hasAttribute
     */
    public function testHasAttribute(): void
    {
        self::assertTrue($this->test->hasAttribute('foo'));
        self::assertFalse($this->test->hasAttribute('bar'));
    }

    /**
     * @covers ::children
     */
    public function testChildren(): void
    {
        self::assertEmpty($this->test->children());

        $this->test->addChild(new Element('foo'));

        self::assertCount(1, $this->test->children());
    }

    /**
     * @covers ::name
     */
    public function testName(): void
    {
        self::assertSame('test', $this->test->name());
    }

    /**
     * @covers ::getAttribute
     */
    public function testGetAttribute(): void
    {
        self::assertSame('bar', $this->test->getAttribute('foo'));
    }

    /**
     * @covers ::getAttribute
     */
    public function testGetAttributeThrowException(): void
    {
        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('Attribute with name not-existing-attribute does not exist');

        $this->test->getAttribute('not-existing-attribute');
    }

    /**
     * @covers ::addChild
     */
    public function testAddChild(): void
    {
        self::assertCount(0, $this->test->children());

        $this->test->addChild(
            new Element('foo'),
            new Element('bar'),
        );

        self::assertCount(2, $this->test->children());
    }

    /**
     * @covers ::getChildByIndex
     */
    public function testGetChildByIndex(): void
    {
        $test = new Element('element4');

        $this->test->addChild(
            new Element('element1'),
            'text',
            new Element('element2'),
            new Element('element3'),
            $test,
            new Element('element5'),
        );

        self::assertSame($test, $this->test->getChildByIndex(4));
    }

    /**
     * @covers ::getChildByIndex
     */
    public function testGetChildByIndexThrowException(): void
    {
        self::expectException(InvalidArgument::class);
        self::expectExceptionMessage('Child with index 3 does not exist');

        $this->test->getChildByIndex(3);
    }
}
