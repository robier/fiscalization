<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Xml;

use Robier\Fiscalization\Exception\InvalidArgument;

final class Element
{
    /** @var Element[] */
    private $children = [];

    public function __construct(private string $name, private array $attributes = [])
    {
        // noop
    }

    public function addChild(self|string|int $element, self|string|int ...$elements): self
    {
        array_unshift($elements, $element);

        foreach ($elements as $element) {
            $this->children[] = $element;
        }

        return $this;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function getChildByName(string $name): self
    {
        foreach ($this->children as $child) {
            if (gettype($child) === 'string') {
                continue;
            }

            if ($child->name() === $name) {
                return $child;
            }
        }

        throw new InvalidArgument("Child with name $name does not exist");
    }

    public function getChildByIndex(int $index): self|string
    {
        $i = 0;
        foreach ($this->children as $child) {
            if ($i === $index) {
                return $child;
            }

            ++$i;
        }

        throw new InvalidArgument("Child with index $index does not exist");
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute(string $name): string
    {
        if (!$this->hasAttribute($name)) {
            throw new InvalidArgument("Attribute with name $name does not exist");
        }

        return $this->attributes[$name];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        $attributes = [];

        foreach ($this->attributes as $name => $value) {
            $attributes[] = "$name=\"$value\"";
        }

        if ($attributes) {
            $attributes = ' ' . implode(' ', $attributes);
        } else {
            $attributes = '';
        }

        $children = '';

        foreach ($this->children as $child) {
            $children .= (string)$child;
        }

        if ($children === '') {
            return <<<XML
            <{$this->name}$attributes/>
            XML;
        }
        return <<<XML
        <{$this->name}$attributes>$children</{$this->name}>
        XML;
    }
}
