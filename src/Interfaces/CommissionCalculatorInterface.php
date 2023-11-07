<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Model\Transaction;

/**
 * Interface for commission calculation.
 * Provides a contract for classes that calculate commissions based on transactions.
 */
interface CommissionCalculatorInterface
{
    /**
     * Calculate the commission for a given transaction.
     *
     * @param Transaction $transaction The transaction for which the commission needs to be calculated.
     * @return string                  Calculated commission amount as a formatted string.
     */
    public function calculate(Transaction $transaction): string;
}
