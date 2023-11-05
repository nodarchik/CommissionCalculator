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
    public function getTransactionsForUserInWeek(int $userId, DateTime $date): array
    {
        $startOfWeek = clone $date;
        $startOfWeek->modify('monday this week');
        $endOfWeek = clone $date;
        $endOfWeek->modify('sunday this week');


        return array_filter($this->transactions, function(Transaction $transaction) use ($userId, $startOfWeek, $endOfWeek) {
            $transactionDate = new DateTime($transaction->getDate());
            return $transaction->getUserId() === $userId
                && $transactionDate >= $startOfWeek
                && $transactionDate <= $endOfWeek;
        });
    }
}
