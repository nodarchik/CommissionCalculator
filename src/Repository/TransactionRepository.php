<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Transaction;
use DateTime;
use Exception;

/**
 * Repository class to manage transactions.
 * Provides methods to add transactions and fetch transactions based on specific criteria.
 */
class TransactionRepository
{
    /**
     * An array to store transactions.
     * @var Transaction[]
     */
    private array $transactions = [];

    /**
     * Add a transaction to the repository.
     *
     * @param Transaction $transaction The transaction to be added.
     */
    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    /**
     * Fetch transactions for a specific user within a week of a given date.
     *
     * @param int      $userId The user ID for whom the transactions are to be fetched.
     * @param DateTime $date   The date within the week of which transactions are to be fetched.
     * @return Transaction[]   An array of transactions matching the criteria.
     */
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
            // Logging the exception for further analysis
            error_log("Error encountered in TransactionRepository: " . $e->getMessage());

            // For safety, return an empty array if an exception occurs
            return [];
        }
    }
}
