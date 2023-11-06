<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Constants\Constants;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;
use App\Service\MathService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Calculator for the commission of private withdrawals.
 */
class WithdrawPrivateCommissionCalculator extends WithdrawCommissionCalculator
{
    /**
     * Math service to perform mathematical operations.
     *
     * @var MathService
     */
    private MathService $mathService;

    /**
     * Counter to track the number of free withdrawals.
     *
     * @var int
     */
    private int $freeWithdrawCount = 0;
    /**
     * Counter to track the total amount of free withdrawals in EUR.
     *
     * @var float
     */
    private float $freeWithdrawAmount = 0.0;

    /**
     * Commission for the current transaction.
     *
     * @var float
     */
    private float $amountForCommission = 0.0;


    /**
     * Constructor to initialize required services and repositories.
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyConverter $currencyConverter,
        MathService $mathService
    ) {
        parent::__construct($transactionRepository, $currencyConverter);
        $this->mathService = $mathService;
    }

    /**
     * Calculate the commission for a given transaction.
     *
     * @throws GuzzleException
     */
    public function calculate(Transaction $transaction): string
    {
        // Reset the counters and commission at the start of each transaction
        $this->resetCounters();
        $this->calculateWeeklyWithdrawals($transaction);

        $amountForCommission = $this->calculateAmountForCommission($transaction);

        return $this->calculateFee($amountForCommission, $transaction->getCurrency());
    }


    /**
     * Calculate the total amount and count of withdrawals for the user in the current week.
     *
     * @throws GuzzleException
     * @throws Exception
     */
    private function calculateWeeklyWithdrawals(Transaction $transaction): void
    {

        $transactionDate = $transaction->getDate();
        $transactionsThisWeek = $this->transactionRepository->getTransactionsForUserInWeek(
            $transaction->getUserId(),
            $transactionDate
        );

        foreach ($transactionsThisWeek as $previousTransaction) {
            $previousTransactionDate = $previousTransaction->getDate();
            if ($previousTransaction->getOperationType() === 'withdraw' &&
                $previousTransactionDate < $transactionDate) {
                $this->freeWithdrawCount++;
                $this->freeWithdrawAmount += $this->currencyConverter->convertAmountToDefaultCurrency(
                    $previousTransaction->getAmount(),
                    $previousTransaction->getCurrency()
                );
            }
        }
    }

    /**
     * Calculate the amount that should be considered for commission.
     *
     * @throws GuzzleException
     */
    private function calculateAmountForCommission(Transaction $transaction): float
    {
        $amountInEur = $this->currencyConverter->convertAmountToDefaultCurrency(
            $transaction->getAmount(),
            $transaction->getCurrency()
        );

        if ($this->freeWithdrawCount < Constants::PRIVATE_FREE_WITHDRAW_COUNT) {
            $remainingFreeAmount = max(Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT - $this->freeWithdrawAmount, 0.0);

            if ($amountInEur <= $remainingFreeAmount) {
                return 0;
            } else {
                return $amountInEur - $remainingFreeAmount;
            }
        }


        return $amountInEur;
    }



    /**
     * Calculate the commission fee based on the amount and currency.
     *
     * @throws GuzzleException
     */
    private function calculateFee(float $amountInEur, string $currency): string
    {
        $fee = bcmul((string)$amountInEur, (string)Constants::PRIVATE_COMMISSION_RATE, Constants::BC_SCALE);

        $feeInTransactionCurrency = $this->currencyConverter->convertAmountFromDefaultCurrency(
            (float)$fee,
            $currency
        );

        $decimals = Constants::CURRENCY_DECIMALS[$currency] ?? Constants::DECIMALS_NUMBER;

        $feeInTransactionCurrency = $this->mathService->bcRoundUp((string)$feeInTransactionCurrency, $decimals);

        return number_format((float)$feeInTransactionCurrency, $decimals, '.', '');
    }

    /**
     * Reset the counters for free withdrawals.
     */
    private function resetCounters(): void
    {
        $this->freeWithdrawCount = 0;
        $this->freeWithdrawAmount = 0.0;
        $this->amountForCommission = 0.0;
    }
}
