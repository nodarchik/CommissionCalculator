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

class WithdrawPrivateCommissionCalculator extends WithdrawCommissionCalculator
{
    private MathService $mathService;
    private int $freeWithdrawCount = 0;
    private float $freeWithdrawAmount = 0.0;
    public function __construct(
        TransactionRepository $transactionRepository,
        CurrencyConverter $currencyConverter,
        MathService $mathService
    ) {
        parent::__construct($transactionRepository, $currencyConverter);
        $this->mathService = $mathService;
    }

    /**
     * @throws GuzzleException
     */
    public function calculate(Transaction $transaction): string
    {
        $this->resetCounters();
        $this->calculateWeeklyWithdrawals($transaction);

        $amountForCommission = $this->calculateAmountForCommission($transaction);

        return $this->calculateFee($amountForCommission, $transaction->getCurrency());
    }

    /**
     * @throws GuzzleException
     */
    private function calculateWeeklyWithdrawals(Transaction $transaction): void
    {
        $transactionDate = $transaction->getDate();
        $transactionsThisWeek = $this->transactionRepository->getTransactionsForUserInWeek(
            $transaction->getUserId(),
            $transactionDate
        );

        foreach ($transactionsThisWeek as $previousTransaction) {
            if ($previousTransaction === $transaction) {
                continue;
            }
            $previousTransactionDate = $previousTransaction->getDate();
            if ($previousTransaction->getOperationType() === 'withdraw' &&
                $previousTransactionDate <= $transactionDate) {
                $this->freeWithdrawCount++;
                $this->freeWithdrawAmount += $this->currencyConverter->convertAmountToDefaultCurrency(
                    $previousTransaction->getAmount(),
                    $previousTransaction->getCurrency()
                );
            }
        }
        $this->freeWithdrawAmount = min($this->freeWithdrawAmount, Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT);
    }


    /**
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
    private function resetCounters(): void
    {
        $this->freeWithdrawCount = 0;
        $this->freeWithdrawAmount = 0.0;
    }
}
