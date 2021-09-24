<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

use DOMDocument;
use OpenSSLAsymmetricKey;
use Robier\Fiscalization\Exception\InvalidArgument;
use Robier\Fiscalization\Xml\Element;

final class Certificate
{
    private array $certificate = [];
    private OpenSSLAsymmetricKey $privateKey;
    private array $publicData;
    private string $purePublicCertificate;
    private string $formattedX509IssuerName;
    private string $formattedX509SerialNumber;

    public function __construct(
        private string $rootCertificatePath,
        string $certificatePath,
        string $certificatePassphrase
    ) {
        if (is_readable($this->rootCertificatePath) === false) {
            throw new InvalidArgument("Root certificate on path $rootCertificatePath not found or not readable");
        }

        if (is_readable($certificatePath) === false) {
            throw new InvalidArgument("Certificate on path $certificatePath not found");
        }

        $certificateContent = @file_get_contents($certificatePath);

        if (openssl_pkcs12_read($certificateContent, $this->certificate, $certificatePassphrase) === false) {
            throw new InvalidArgument('Could not read certificate store, passphrase could be wrong');
        }

        $this->privateKey = openssl_pkey_get_private($this->certificate['pkey'], $certificatePassphrase);
        $this->publicData = openssl_x509_parse($this->certificate['cert']);

        $this->purePublicCertificate = str_replace(['-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----', "\n"], '', $this->certificate['cert']);
        $this->formattedX509IssuerName = sprintf('OU=%s,O=%s,C=%s', $this->publicData['issuer']['OU'] ?? 'DEMO', $this->publicData['issuer']['O'], $this->publicData['issuer']['C']);
        $this->formattedX509SerialNumber = sprintf('%0.0F', hexdec($this->publicData['serialNumber']));
    }

    public function generateSecurityCode(Bill $bill): string
    {
        $data = '';
        $data .= $bill->company()->oib()->value();
        $data .= $bill->createdAt()->format('d.m.Y H:i:s');
        $data .= $bill->identifier()->billNumber();
        $data .= $bill->identifier()->storeDesignation();
        $data .= $bill->identifier()->tollDeviceNumber();
        $data .= sprintf('%.2f', $bill->totalAmount() / 100);

        return md5($this->sign($data));
    }

    private function canonicalizeXml(Element $elements): string
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->loadXML((string)$elements);

        return $xml->C14N();
    }

    private function sign(string $data): string
    {
        $signatureValue = '';
        openssl_sign($data, $signatureValue, $this->privateKey, OPENSSL_ALGO_SHA1);

        return $signatureValue;
    }

    public function signXml(Element $elements): void
    {
        if ($elements->hasAttribute('Id') === false) {
            throw new InvalidArgument('Element provided is missing "Id" attribute');
        }

        $id = '#' . $elements->getAttribute('Id');
        $digestValue = base64_encode(hash('sha1', $this->canonicalizeXml($elements), true));


        $signatureXml = (new Element('Signature', ['xmlns' => 'http://www.w3.org/2000/09/xmldsig#']))
            ->addChild((new Element('SignedInfo', ['xmlns' => 'http://www.w3.org/2000/09/xmldsig#']))
                ->addChild(new Element('CanonicalizationMethod', ['Algorithm' => 'http://www.w3.org/2001/10/xml-exc-c14n#']))
                ->addChild(new Element('SignatureMethod', ['Algorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1']))
                ->addChild((new Element('Reference', ['URI' => $id]))
                    ->addChild((new Element('Transforms'))
                        ->addChild(new Element('Transform', ['Algorithm' => 'http://www.w3.org/2000/09/xmldsig#enveloped-signature']))
                        ->addChild(new Element('Transform', ['Algorithm' => 'http://www.w3.org/2001/10/xml-exc-c14n#']))
                    )
                    ->addChild((new Element('DigestMethod', ['Algorithm' => 'http://www.w3.org/2000/09/xmldsig#sha1'])))
                    ->addChild((new Element('DigestValue'))->addChild($digestValue))
                )
            );

        $signatureValue = $this->sign($this->canonicalizeXml($signatureXml->getChildByIndex(0)));

        $signatureXml
            ->addChild((new Element('SignatureValue'))->addChild(base64_encode($signatureValue)))
            ->addChild((new Element('KeyInfo'))
                ->addChild((new Element('X509Data'))
                    ->addChild((new Element('X509Certificate'))->addChild($this->purePublicCertificate))
                    ->addChild((new Element('X509IssuerSerial'))
                        ->addChild((new Element('X509IssuerName'))->addChild($this->formattedX509IssuerName))
                        ->addChild((new Element('X509SerialNumber'))->addChild($this->formattedX509SerialNumber))
                    )
                )
            );

        $elements->addChild($signatureXml);
    }

    public function rootCertificatePath(): string
    {
        return $this->rootCertificatePath;
    }
}
