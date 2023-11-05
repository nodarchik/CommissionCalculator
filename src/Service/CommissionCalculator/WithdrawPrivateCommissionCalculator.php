<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Constants\Constants;
use App\Interfaces\PrivateWithdrawCalculatorInterface;
use App\Model\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CurrencyConverter;
use App\Service\MathService;
use DateTime;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Calculator for the commission of private withdrawals.
 */
class WithdrawPrivateCommissionCalculator extends WithdrawCommissionCalculator implements
    PrivateWithdrawCalculatorInterface
{
    /** @var MathService */
    private MathService $mathService;

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
     * @throws Exception
     */
    public function calculate(Transaction $transaction): string
    {
        $transactionDate = new DateTime($transaction->getDate());

        // Explicitly determine the start and end of the week
        $startOfWeek = clone $transactionDate;
        $startOfWeek->modify('monday this week');
        $endOfWeek = clone $transactionDate;
        $endOfWeek->modify('sunday this week');

        $transactionsThisWeek = $this->transactionRepository->getTransactionsForUserInWeek(
            $transaction->getUserId(),
            $transactionDate
        );

        $freeWithdrawCount = 0;
        $freeWithdrawAmount = 0.0;

        foreach ($transactionsThisWeek as $previousTransaction) {
            // Exclude the current transaction from the count
            if ($previousTransaction->getOperationType() === 'withdraw' &&
                $previousTransaction->getDate() !== $transaction->getDate()) {
                $freeWithdrawCount++;
                $freeWithdrawAmount += $this->currencyConverter->convertAmountToDefaultCurrency(
                    $previousTransaction->getAmount(),
                    $previousTransaction->getCurrency()
                );
            }
        }

        $amountInEur = $this->currencyConverter->convertAmountToDefaultCurrency(
            $transaction->getAmount(),
            $transaction->getCurrency()
        );

        // If it's among the first 3 transactions
        if ($freeWithdrawCount < Constants::PRIVATE_FREE_WITHDRAW_COUNT) {
            $remainingFreeAmount = Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT - $freeWithdrawAmount;

            // Calculate the exceeded amount for commission
            $amountForCommission = ($amountInEur <= $remainingFreeAmount)
                ? 0
                : $amountInEur - $remainingFreeAmount;
        } else {
            // For the 4th and subsequent transactions
            $amountForCommission = $amountInEur;
        }

        return $this->calculateFee($amountForCommission, $transaction->getCurrency());
    }

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

    public function setFreeWithdrawCount(int $count): void
    {
        // This method remains unimplemented as we've moved the logic to the `calculate` method.
    }

    public function setFreeWithdrawAmount(float $amount): void
    {
        // This method remains unimplemented as we've moved the logic to the `calculate` method.
    }
}
