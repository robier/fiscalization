<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Client;

use DOMDocument;
use DOMNode;
use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Certificate;
use Robier\Fiscalization\Exception;
use Robier\Fiscalization\Exception\CommunicationError;
use Robier\Fiscalization\Exception\CurlError;
use Robier\Fiscalization\Exception\PingError;
use Robier\Fiscalization\Xml\Element;

class Production implements Contract
{
    protected XmlConverter $xmlConverter;

    public function __construct(
        protected string $url,
        protected Certificate $certificate
    ) {
        $this->xmlConverter = new XmlConverter();
    }

    public function send(Bill $bill): Response
    {
        $xmlElements = (
            (new Element(
                'tns:RacunZahtjev',
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

        $xmlResponse = $this->makeRequest($request);

        $xmlRequest = new DOMDocument();
        $xmlRequest->loadXML($request);

        return new Response(
            $xmlResponse->getElementsByTagName('Jir')->item(0)->nodeValue,
            $xmlRequest->getElementsByTagName('ZastKod')->item(0)->nodeValue
        );
    }

    public function ping(): void
    {
        $request = (new Element('tns:EchoRequest', [
            'xmlns:tns' => 'http://www.apis-it.hr/fin/2012/types/f73',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.apis-it.hr/fin/2012/types/f73 FiskalizacijaSchema.xsd '
        ]))->addChild('ping');

        try {
            $this->makeRequest((string)SoapEnvelope::fromElements($request));
        } catch (Exception $error) {
            throw new PingError();
        }
    }

    protected function makeRequest(string $data): DOMDocument
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL => $this->url,
            CURLOPT_CONNECTTIMEOUT => 500,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CAINFO => $this->certificate->rootCertificatePath(),
            CURLOPT_CAPATH => $this->certificate->rootCertificatePath(),
            CURLOPT_FRESH_CONNECT => true,
        ];

        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) !== 0) {
            throw new CurlError(curl_error($ch), curl_errno($ch));
        }

        curl_close($ch);

        $xmlResponse = new DOMDocument();
        $xmlResponse->loadXML($response);

        if ($code !== 200) {
            throw $this->generateErrorException($xmlResponse);
        }

        return $xmlResponse;
    }

    protected function generateErrorException(DOMDocument $response): ?CommunicationError
    {
        $errorElements = $response->getElementsByTagName('Greske');
        $errors = [];
        /** @var DOMNode $child */
        foreach ($errorElements as $child) {
            /** @var DOMNode $grandChild */
            foreach ($child->childNodes as $grandChild) {
                $errorContent = [
                    'code' => null,
                    'message' => null,
                ];
                /** @var DOMNode $grandGrandChild */
                foreach ($grandChild->childNodes as $grandGrandChild) {
                    if (in_array($grandGrandChild->nodeName, ['tns:SifraGreske', 'f73:SifraGreske'], true)) {
                        $errorContent['code'] = $grandGrandChild->nodeValue;
                    }

                    if (in_array($grandGrandChild->nodeName, ['tns:PorukaGreske', 'f73:PorukaGreske'], true)) {
                        $errorContent['message'] = $grandGrandChild->nodeValue;
                    }
                }
                $errors += [$errorContent['code'] => $errorContent['message']];
            }
        }

        $exception = new CommunicationError(...$errors);

        if ($exception->hasErrors()) {
            return $exception;
        }

        return null;
    }
}
