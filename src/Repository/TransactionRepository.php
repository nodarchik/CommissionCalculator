<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Transaction;

class TransactionRepository
{
    private array $transactions = [];

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
    public function findTransactionsByUserIdAndWeek(int $userId, string $weekStart, string $weekEnd): array
    {
        return array_filter($this->transactions, function(Transaction $transaction) use ($userId, $weekStart, $weekEnd) {
            return $transaction->getUserId() === $userId &&
                $transaction->getDate() >= $weekStart &&
                $transaction->getDate() <= $weekEnd;
        });
    }

    public function findTransactionsByUserId(int $userId): array
    {
        return array_filter($this->transactions, function(Transaction $transaction) use ($userId) {
            return $transaction->getUserId() === $userId;
        });
    }

    public function findAllTransactions(): array
    {
        return $this->transactions;
    }

    public function updateTransaction(Transaction $updatedTransaction): void
    {
        foreach ($this->transactions as $index => $transaction) {
            if ($transaction->getUserId() === $updatedTransaction->getUserId() &&
                $transaction->getDate() === $updatedTransaction->getDate()) {
                $this->transactions[$index] = $updatedTransaction;
                return;
            }
        }
    }

    public function deleteTransaction(Transaction $transactionToDelete): void
    {
        $this->transactions = array_filter($this->transactions, function(Transaction $transaction) use ($transactionToDelete) {
            return !($transaction->getUserId() === $transactionToDelete->getUserId() &&
                $transaction->getDate() === $transactionToDelete->getDate());
        });
    }
}
