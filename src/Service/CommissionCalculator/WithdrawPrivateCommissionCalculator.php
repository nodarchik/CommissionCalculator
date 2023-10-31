<?php

declare(strict_types=1);

namespace App\Service\CommissionCalculator;

use App\Model\Transaction;
use App\Service\CurrencyConverter;
use App\Constants\Constants;
use App\Repository\TransactionRepository;
use App\Interfaces\WithdrawCalculatorInterface;
class WithdrawPrivateCommissionCalculator extends WithdrawCommissionCalculator implements WithdrawCalculatorInterface
{
    // New properties to keep track of free withdrawals and their amounts
    private int $freeWithdrawCount = 0;
    private float $freeWithdrawAmount = 0.0;
    public function setFreeWithdrawCount(int $count): void
    {
        $this->freeWithdrawCount = $count;
    }

    public function setFreeWithdrawAmount(float $amount): void
    {
        $this->freeWithdrawAmount = $amount;
    }
    public function __construct(TransactionRepository $transactionRepository, CurrencyConverter $currencyConverter)
    {
        parent::__construct($transactionRepository, $currencyConverter);
    }

    public function calculate(Transaction $transaction): string
    {
        $amountInEur = $this->currencyConverter->convertAmountToDefaultCurrency(
            $transaction->getAmount(),
            $transaction->getCurrency()
        );

        // New Logic for free withdrawals
        if ($this->freeWithdrawCount < Constants::PRIVATE_FREE_WITHDRAW_COUNT) {
            $remainingFreeAmount = Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT - $this->freeWithdrawAmount;

            if ($amountInEur <= $remainingFreeAmount) {
                $this->freeWithdrawAmount += $amountInEur;
                $this->freeWithdrawCount++;
                return '0.00';  // No fee
            } else {
                $amountInEur -= $remainingFreeAmount;
                $this->freeWithdrawAmount = Constants::PRIVATE_FREE_WITHDRAW_AMOUNT_LIMIT;
            }
        }

        $fee = $amountInEur * Constants::PRIVATE_COMMISSION_RATE;

        $feeInTransactionCurrency = $this->currencyConverter->convertAmountFromDefaultCurrency(
            $fee,
            $transaction->getCurrency()
        );

        $decimals = Constants::CURRENCY_DECIMALS[$transaction->getCurrency()] ?? Constants::DECIMALS_NUMBER;
        $feeInTransactionCurrency = ceil($feeInTransactionCurrency * pow(10, $decimals)) / pow(10, $decimals);

        return number_format($feeInTransactionCurrency, $decimals, '.', '');
    }
}
