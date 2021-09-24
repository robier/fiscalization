<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

use Robier\Fiscalization\Xml\Element;

/**
 * @internal
 */
final class SoapEnvelope
{
    public function __construct(protected string $data)
    {
    }

    public static function fromElements(Element $element): self
    {
        return new static((string)$element);
    }

    public function __toString(): string
    {
        return <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Body>{$this->data}</soapenv:Body></soapenv:Envelope>
        
        XML;
    }
}
