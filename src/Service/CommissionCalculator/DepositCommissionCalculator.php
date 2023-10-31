<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Constants\Constants;

class DepositCommissionCalculator implements CommissionCalculatorInterface
{
    public function calculate(Transaction $transaction): string
    {
        // Using bcmul for precise multiplication
        $fee = bcmul((string)$transaction->getAmount(), (string)Constants::DEPOSIT_FEE, Constants::BC_SCALE);

        // Using a custom function to round up the fee to the nearest currency decimal places
        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $fee = $this->bcRoundUp($fee, $decimals);  // Use $this-> to call the method

        return number_format($fee, $decimals, '.', '');
    }

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
