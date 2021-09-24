# Croatian Fiscalization

Library for connecting your shop to the Croatian FINA.

### Before usage

There are 2 types of certificates **demo** and **production**. Depending on type of certificate,
you need to use different client as demo and production FINA environments has different URLs.
No matter the type, there are 2 certificates needed for this library to work properly. Root
certificate (that are publicly available for download) and personal one that is used for
signing the payload before sending it to the FINA.

Download [DEMO](https://www.fina.hr/fina-demo-ca-certifikati) or [PRODUCTION](https://www.fina.hr/ca-fina-root-certifikati) root certificate.
Certificate should be in TXT format.

Get [DEMO](https://www.fina.hr/demo-certifikati) or [PRODUCTION](https://www.fina.hr/fiskalizacija) certificate by applying for it.
File will be in p12 format.

When you finish procedure, you should have 3 items:
- FINA root certificate
- your own certificate
- passphrase for your own certificate

Those 3 items are necessary to successfully communicate with FINA servers.

-----------

Example:

```php

use Robier\Fiscalization\Bill;
use Robier\Fiscalization\Certificate;
use Robier\Fiscalization\Client;
use Robier\Fiscalization\Company;
use Robier\Fiscalization\Oib;
use Robier\Fiscalization\Operator;
use Robier\Fiscalization\Refund;
use Robier\Fiscalization\Tax;

$company = new Company(new Oib('96918429930'), true);
$operator = new Operator(new Oib('07392314075'));

$id = new Bill\Identifier(23, 'MAGE5', 1);

$bill = new Bill(
    $company,
    $operator,
    new DateTimeImmutable(),
    $id,
    Bill\PaymentType::cash(),
    Bill\SequenceType::shop()
);

$bill
    ->addTax(new Tax\Vat(100_00, 25_00, 25_00))
    ->addTax(new Tax\Vat(50_00, 5_00, 2_50))
    ->addTax(new Tax\Vat(300_00, 25_00, 75_00))
    ->addTax(new Tax\Vat(400_00, 25_00, 100_00))
    ->addTax(new Tax\ConsumptionTax(600_00, 3_00, 18_00))
    ->setTaxFreeAmount(100_00)
    ->setMarginTaxAmount(50_00)
    ->addRefund(new Refund('Povratna naknada', 10_00))
;

$certificate = new Certificate(
    __DIR__ . '/config/certificates/demo_root.txt', // root certificate path
    __DIR__ . '/config/certificates/FISKAL_1.p12', // private certificate path
    '************' // private certificate passphrase
);

$client = Client::demo($certificate);

$client->send($bill); // sends bill to FINA
$client->check($bill); // checks bill trough demo checking (available only in DEMO client)
$client->ping(); // checks if server is avaliable
```

### Docker

Local development should be done via docker.

Build docker container:
```bash
docker/build
```

After you build your docker container, you can go inside and use it via:
```bash
docker/run ash
```

Or run command inside docker from outside via:

```bash
docker/run composer install
```

### Tests

Everything should be tested. You can run tests with command:

```bash
docker/run vendor/bin/phpunit
```