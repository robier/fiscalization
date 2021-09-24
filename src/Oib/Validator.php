<?php

declare(strict_types=1);

namespace Robier\Fiscalization\Oib;

final class Validator
{
    public function valid(string $oib): bool
    {
        if (strlen($oib) !== 11 || !is_numeric($oib)) {
            return false;
        }

        $a = 10;

        for ($i = 0; $i < 10; $i++) {
            $a += (int)$oib[$i];
            $a %= 10;

            if ($a == 0) {
                $a = 10;
            }

            $a *= 2;
            $a %= 11;
        }

        $controlNumber = 11 - $a;

        if ($controlNumber == 10) {
            $controlNumber = 0;
        }

        return $controlNumber === intval(substr($oib, 10, 1), 10);
    }
}
