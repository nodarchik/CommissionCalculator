<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Transaction;
use DateTime;
use Exception;

class TransactionRepository
{
    private array $transactions = [];
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
    public function getTransactionsForUserInWeek(int $userId, DateTime $date): array
    {
        try {
            $startOfWeek = clone $date;
            $startOfWeek->modify('monday this week')->setTime(0, 0);

            $endOfWeek = clone $date;
            $endOfWeek->modify('sunday this week')->setTime(23, 59, 59);

            // Convert DateTime objects to string format for comparison
            $startOfWeekStr = $startOfWeek->format('Y-m-d H:i:s');
            $endOfWeekStr = $endOfWeek->format('Y-m-d H:i:s');

            return array_filter(
                $this->transactions,
                function (Transaction $transaction) use ($userId, $startOfWeekStr, $endOfWeekStr) {
                    $transactionDateStr = $transaction->getDate()->format('Y-m-d H:i:s');
                    return $transaction->getUserId() === $userId
                        && $transactionDateStr >= $startOfWeekStr
                        && $transactionDateStr <= $endOfWeekStr;
                }
            );
        } catch (Exception $e) {
            error_log("Error encountered in TransactionRepository: " . $e->getMessage());
            return [];
        }
    }
}
