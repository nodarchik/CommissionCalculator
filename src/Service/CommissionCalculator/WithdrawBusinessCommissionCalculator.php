<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Constants\Constants;

class WithdrawBusinessCommissionCalculator implements CommissionCalculatorInterface
{
    use BCRoundUpTrait;

    public function calculate(Transaction $transaction): string
    {
        $fee = bcmul((string)$transaction->getAmount(), (string)Constants::BUSINESS_WITHDRAW_FEE, Constants::BC_SCALE);
        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $fee = $this->bcRoundUp($fee, $decimals);
        return number_format((float)$fee, $decimals, '.', '');
    }
}