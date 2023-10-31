<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Constants\Constants;

class WithdrawBusinessCommissionCalculator implements CommissionCalculatorInterface
{
    public function calculate(Transaction $transaction): string
    {
        $fee = $transaction->getAmount() * Constants::BUSINESS_WITHDRAW_FEE;

        // Round up the fee to the nearest currency decimal places
        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $fee = ceil($fee * pow(10, $decimals)) / pow(10, $decimals);

        return number_format($fee, $decimals, '.', '');
    }
}
