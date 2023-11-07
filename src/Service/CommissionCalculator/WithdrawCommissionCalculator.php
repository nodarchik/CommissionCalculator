<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;

/**
 * Abstract class for withdrawal commission calculation.
 * This class provides a base for calculators handling withdrawal commissions,
 * requiring subclasses to implement specific calculation logic according to the type of withdrawal.
 */
abstract class WithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    /**
     * Repository for accessing transaction data.
     * @var TransactionRepository
     */
    protected TransactionRepository $transactionRepository;
    /**
     * Service for handling currency conversion.
     * @var CurrencyConverter
     */
    protected CurrencyConverter $currencyConverter;
    /**
     * Constructor to set up the transaction repository and currency converter.
     *
     * @param TransactionRepository $transactionRepository Repository to access transaction data.
     * @param CurrencyConverter     $currencyConverter     Service to perform currency conversion.
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyConverter $currencyConverter
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->currencyConverter = $currencyConverter;
    }
}
