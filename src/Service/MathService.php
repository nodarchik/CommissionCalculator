<?php

// Enforce strict typing mode for better type safety.
declare(strict_types=1);

// Define the namespace for the current class.
namespace App\Service;

/**
 * Provides mathematical utilities with high precision using BC Math functions.
 */
class MathService
{
    /**
     * Round up a number to the specified precision using BC Math.
     * If the number is already rounded to the desired precision, it returns the original number.
     *
     * @param string $number     The number to round up.
     * @param int    $precision  The desired precision after rounding.
     * @return string            The rounded number as a string to maintain precision.
     */
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
