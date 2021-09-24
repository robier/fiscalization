<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Xml\Element;

final class Demo extends Production
{
    public function check(Bill $bill): void
    {
        $xmlElements = (
            (new Element(
                'tns:ProvjeraZahtjev',
                [
                    'xmlns:tns' => 'http://www.apis-it.hr/fin/2012/types/f73',
                    'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
                    'xsi:schemaLocation' => 'http://www.apis-it.hr/fin/2012/types/f73 ../schema/FiskalizacijaSchema.xsd',
                    'Id' => uniqid(),
                ])
            )->addChild(
                ...$this->xmlConverter->encode(
                    $bill,
                    $this->certificate->generateSecurityCode($bill)
                )
            )
        );

        $this->certificate->signXml($xmlElements);

        $request = (string)SoapEnvelope::fromElements($xmlElements);

        $errorException = $this->generateErrorException(
            $this->makeRequest($request)
        );

        if ($errorException !== null) {
            throw  $errorException;
        }
    }
}
