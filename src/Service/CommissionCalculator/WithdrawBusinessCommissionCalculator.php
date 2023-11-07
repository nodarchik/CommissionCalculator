<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Constants\Constants;
use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Service\MathService;

/**
 * Calculator for the commission of business withdrawals.
 * Extends the WithdrawCommissionCalculator to provide specific logic for business withdrawals.
 */
class WithdrawBusinessCommissionCalculator implements CommissionCalculatorInterface
{
    private MathService $mathService;
    public function __construct(MathService $mathService)
    {
        $this->mathService = $mathService;
    }
    /**
     * Calculate the commission for a given business withdrawal transaction.
     * Logic to calculate the commission specific to business withdrawals.
     *
     * @param Transaction $transaction
     * @return string               Commission amount as a formatted string.
     */
    public function calculate(Transaction $transaction): string
    {
        $fee = bcmul((string)$transaction->getAmount(), (string)Constants::BUSINESS_WITHDRAW_FEE, Constants::BC_SCALE);
        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $fee = $this->mathService->bcRoundUp($fee, $decimals);
        return number_format((float)$fee, $decimals, '.', '');
    }
}
