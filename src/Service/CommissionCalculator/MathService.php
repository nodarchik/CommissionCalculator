<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

class MathService
{
    // Custom function to round up using BC Math
    public function bcRoundUp(string $number, int $precision = 0): string
    {
        if ($precision < 0) {
            return $number;  // No rounding needed
        }

        $factor = bcpow('10', (string)$precision);
        $temp = bcmul($number, $factor);

        // Check if the number already has no decimal parts
        if (bccomp($temp, bcadd($temp, '0', 0)) === 0) {
            return bcdiv($temp, $factor, $precision);
        }

        // Round up
        $rounded = bcadd($temp, '1', 0);
        return bcdiv($rounded, $factor, $precision);
    }
}