<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Transaction;
use DateTime;

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
    public function getTransactionsForUserInWeek(int $userId, DateTime $date): array
    {
        $startOfWeek = clone $date;
        $startOfWeek->modify('monday this week');

        $endOfWeek = clone $date;
        $endOfWeek->modify('sunday this week');

        return array_filter($this->transactions, function(Transaction $transaction) use ($userId, $startOfWeek, $endOfWeek) {
            return $transaction->getUserId() === $userId
                && $transaction->getDate() >= $startOfWeek
                && $transaction->getDate() <= $endOfWeek;
        });
    }
}
