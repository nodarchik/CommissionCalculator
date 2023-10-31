<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use App\Utils\CSVReader;
use App\Utils\CSVWriter;

class TransactionController
{
    private TransactionService $transactionService;
    private CSVReader $csvReader;
    private CSVWriter $csvWriter;

    public function __construct(
        TransactionService $transactionService,
        CSVReader $csvReader,
        CSVWriter $csvWriter
    ) {
        $this->transactionService = $transactionService;
        $this->csvReader = $csvReader;
        $this->csvWriter = $csvWriter;
    }

    public function processTransactions(string $inputFilePath, string $outputFilePath): void
    {
        $transactions = $this->csvReader->read($inputFilePath);
        $commissions = [];

        foreach ($transactions as $transaction) {
            $commission = $this->transactionService->processTransaction($transaction);
            $commissions[] = $commission;
        }

        $this->csvWriter->write($outputFilePath, $commissions);
    }
}
