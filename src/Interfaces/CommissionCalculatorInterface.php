<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Model\Transaction;

interface CommissionCalculatorInterface
{
    public function calculate(Transaction $transaction): string;
}
