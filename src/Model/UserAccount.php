<?php

namespace App\Model;

class UserAccount
{
    private int $userId;
    private array $transactions = [];

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }
}
