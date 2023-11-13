<?php

declare(strict_types=1);

namespace App\Service;

class MathService
{
    public function bcRoundUp(string $number, int $precision = 0): string
    {
        if ($precision < 0) {
            return $number;
        }

        $factor = bcpow('10', (string)$precision);
        $temp = bcmul($number, $factor);
        if (bccomp($temp, bcadd($temp, '0', 0)) === 0) {
            return bcdiv($temp, $factor, $precision);
        }

        $rounded = bcadd($temp, '1', 0);
        return bcdiv($rounded, $factor, $precision);
    }
}
