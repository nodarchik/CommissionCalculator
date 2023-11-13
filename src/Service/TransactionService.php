<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;

class TransactionService
{
    private CommissionCalculatorInterface $depositCalculator;
    private CommissionCalculatorInterface $withdrawPrivateCalculator;
    private CommissionCalculatorInterface $withdrawBusinessCalculator;
    private TransactionRepository $transactionRepository;
    private CurrencyConverter $currencyConverter;
    public function __construct(
        CommissionCalculatorInterface $depositCalculator,
        CommissionCalculatorInterface $withdrawPrivateCalculator,
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

        return $calculator->calculate($transaction);
    }
    private function selectCalculator(Transaction $transaction): CommissionCalculatorInterface
    {
        if ($transaction->getOperationType() === 'deposit') {
            return $this->depositCalculator;
        }

        return $transaction->getUserType() === 'private'
            ? $this->withdrawPrivateCalculator
            : $this->withdrawBusinessCalculator;
    }
}
