<?php

declare(strict_types=1);

namespace App\Interfaces;

interface WithdrawCalculatorInterface extends CommissionCalculatorInterface
{
    public function setFreeWithdrawCount(int $count): void;
    public function setFreeWithdrawAmount(float $amount): void;
}
