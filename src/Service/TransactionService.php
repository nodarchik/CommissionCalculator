<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;
use App\Interfaces\WithdrawCalculatorInterface;

class TransactionService
{
    private CommissionCalculatorInterface $depositCalculator;
    private WithdrawCalculatorInterface $withdrawPrivateCalculator;
    private CommissionCalculatorInterface $withdrawBusinessCalculator;
    private CurrencyConverter $currencyConverter;
    private TransactionRepository $transactionRepository;

    public function __construct(
        CommissionCalculatorInterface $depositCalculator,
        WithdrawCalculatorInterface $withdrawPrivateCalculator,
        CommissionCalculatorInterface $withdrawBusinessCalculator,
        CurrencyConverter $currencyConverter,
        TransactionRepository $transactionRepository
    ) {
        $this->depositCalculator = $depositCalculator;
        $this->withdrawPrivateCalculator = $withdrawPrivateCalculator;
        $this->withdrawBusinessCalculator = $withdrawBusinessCalculator;
        $this->currencyConverter = $currencyConverter;
        $this->transactionRepository = $transactionRepository;
    }

    public function processTransaction(Transaction $transaction): string
    {
        $this->transactionRepository->addTransaction($transaction);

        $calculator = $this->selectCalculator($transaction);

        // If the transaction is a withdrawal by a private user, update the state of the private withdrawal calculator
        if ($transaction->getUserType() === 'private' && $transaction->getOperationType() === 'withdraw') {
            // ... (Rest of the code remains the same)
        }

        return $calculator->calculate($transaction);
    }

    private function selectCalculator(Transaction $transaction): CommissionCalculatorInterface
    {
        if ($transaction->getOperationType() === 'deposit') {
            return $this->depositCalculator;
        }

        if ($transaction->getUserType() === 'private') {
            return $this->withdrawPrivateCalculator;
        }

        return $this->withdrawBusinessCalculator;
    }
}
