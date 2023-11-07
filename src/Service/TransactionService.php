<?php

// Enforce strict typing mode for better type safety.
declare(strict_types=1);

// Define the namespace for the current class.
namespace App\Service;

// Import required classes and interfaces.
use App\Interfaces\CommissionCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;

/**
 * Service responsible for processing transactions.
 * This service manages the transaction flow, from adding the transaction to selecting the right calculator for commission.
 */
class TransactionService
{
    // Dependency for deposit commission calculation.
    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $depositCalculator;

    // Dependency for withdrawal commission calculation for private users.
    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $withdrawPrivateCalculator;

    // Dependency for withdrawal commission calculation for business users.
    /** @var CommissionCalculatorInterface */
    private CommissionCalculatorInterface $withdrawBusinessCalculator;

    // Dependency to interact with the transaction storage.
    /** @var TransactionRepository */
    private TransactionRepository $transactionRepository;

    // Dependency to convert currencies.
    /** @var CurrencyConverter */
    private CurrencyConverter $currencyConverter;

    /**
     * Constructor to initialize all dependencies.
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
     * Process a given transaction by adding it to the repository and calculating commission.
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
     * Select the appropriate calculator based on the transaction type.
     * For deposits, it uses the deposit calculator.
     * For withdrawals, it determines the calculator based on user type (private or business).
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
