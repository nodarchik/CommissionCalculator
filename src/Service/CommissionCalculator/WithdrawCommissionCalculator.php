<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;

abstract class WithdrawCommissionCalculator implements CommissionCalculatorInterface
{
    protected TransactionRepository $transactionRepository;
    protected CurrencyConverter $currencyConverter;
    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyConverter $currencyConverter
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->currencyConverter = $currencyConverter;
    }
}
