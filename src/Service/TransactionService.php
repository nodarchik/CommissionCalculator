<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;

/**
 * Service responsible for processing transactions.
 */
class TransactionService
{
    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $depositCalculator;

    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $withdrawPrivateCalculator;

    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $withdrawBusinessCalculator;

    /** @var TransactionRepository */
    private TransactionRepository $transactionRepository;

    /** @var CurrencyConverter */
    private CurrencyConverter $currencyConverter;

    /**
     * Constructor.
     *
     * @param CommissionCalculatorInterface $depositCalculator
     * @param CommissionCalculatorInterface $withdrawPrivateCalculator
     * @param CommissionCalculatorInterface $withdrawBusinessCalculator
     * @param CurrencyConverter             $currencyConverter
     * @param TransactionRepository         $transactionRepository
     */
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

    /**
     * Process a given transaction.
     *
     * @param Transaction $transaction
     * @return string
     */
    public function processTransaction(Transaction $transaction): string
    {
        $this->transactionRepository->addTransaction($transaction);
        $calculator = $this->selectCalculator($transaction);

        return $calculator->calculate($transaction);
    }

    /**
     * Select the appropriate calculator based on the transaction.
     *
     * @param Transaction $transaction
     * @return CommissionCalculatorInterface
     */
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
