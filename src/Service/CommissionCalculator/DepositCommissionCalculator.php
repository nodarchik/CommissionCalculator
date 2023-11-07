<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Constants\Constants;
use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Service\MathService;

/**
 * Calculator for the commission of deposits.
 * Implements the CommissionCalculatorInterface to provide specific logic for deposit commissions.
 */
class DepositCommissionCalculator implements CommissionCalculatorInterface
{
    private MathService $mathService;
    public function __construct(MathService $mathService)
    {
        $this->mathService = $mathService;
    }

    /**
     * Calculate the commission for a given deposit transaction.
     * Logic to calculate the commission specific to deposits.
     *
     * @param Transaction $transaction
     * @return string               Commission amount as a formatted string.
     */
    public function calculate(Transaction $transaction): string
    {
        $fee = bcmul((string)$transaction->getAmount(), (string)Constants::DEPOSIT_FEE, Constants::BC_SCALE);
        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $fee = $this->mathService->bcRoundUp($fee, $decimals);
        return number_format((float) $fee, $decimals, '.', '');
    }
}
