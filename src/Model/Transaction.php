<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;

class Transaction
{
    private string $userId;
    private string $userType;
    private string $operationType;
    private string $amount;
    private string $currency;
    private DateTime $date;

    public function __construct(
        string $userId,
        string $userType,
        string $operationType,
        string $amount,
        string $currency,
        DateTime $date
    ) {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->date = $date;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUserType(): string
    {
        return $this->userType;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }
}
