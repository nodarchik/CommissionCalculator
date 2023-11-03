<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Model\Transaction;
use App\Service\CurrencyConverter;
use App\Constants\Constants;
use App\Repository\TransactionRepository;
use App\Interfaces\WithdrawCalculatorInterface;
use App\Service\CommissionCalculator\MathService;
use DateTime;
use Exception;

class WithdrawPrivateCommissionCalculator extends WithdrawCommissionCalculator implements WithdrawCalculatorInterface
{
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
     * @throws Exception
     */
    public function calculate(Transaction $transaction): string
    {
        $transactionDate = new DateTime($transaction->getDate());

        $transactionsThisWeek = $this->transactionRepository->getTransactionsForUserInWeek(
            $transaction->getUserId(),
            $transactionDate
        );

        $freeWithdrawCount = 0;
        $freeWithdrawAmount = 0.0;

        foreach ($transactionsThisWeek as $previousTransaction) {
            if ($previousTransaction->getOperationType() === 'withdraw') {
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

        if ($freeWithdrawCount < Constants::PRIVATE_FREE_WITHDRAW_COUNT) {
            $remainingFreeAmount = Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT - $freeWithdrawAmount;

            if ($amountInEur <= $remainingFreeAmount) {
                return '0.00';  // No fee
            } else {
                $amountInEur -= $remainingFreeAmount;
            }
        }

        $fee = bcmul((string)$amountInEur, (string)Constants::PRIVATE_COMMISSION_RATE, Constants::BC_SCALE);

        $feeInTransactionCurrency = $this->currencyConverter->convertAmountFromDefaultCurrency(
            (float)$fee,
            $transaction->getCurrency()
        );

        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;

        $feeInTransactionCurrency = $this->mathService->bcRoundUp((string)$feeInTransactionCurrency, $decimals);

        return number_format((float)$feeInTransactionCurrency, $decimals, '.', '');
    }

    public function setFreeWithdrawCount(int $count): void
    {
        // TODO: Implement setFreeWithdrawCount() method.
    }

    public function setFreeWithdrawAmount(float $amount): void
    {
        // TODO: Implement setFreeWithdrawAmount() method.
    }
}
