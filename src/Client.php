<?php

declare(strict_types=1);

namespace Robier\Fiscalization;

final class Client
{
    public static function production(Certificate $certificate): Client\Production
    {
        return new Client\Production(
            'https://cis.porezna-uprava.hr:8449/FiskalizacijaService',
            $certificate
        );
    }

    public static function demo(Certificate $certificate): Client\Demo
    {
        return new Client\Demo(
            'https://cistest.apis-it.hr:8449/FiskalizacijaServiceTest',
            $certificate
        );
    }
}
