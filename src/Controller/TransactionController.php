<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use App\Utils\CSVReader;
use Exception;

class TransactionController
{
    private TransactionService $transactionService;
    private CSVReader $csvReader;

    public function __construct(
        TransactionService $transactionService,
        CSVReader $csvReader,
    ) {
        $this->transactionService = $transactionService;
        $this->csvReader = $csvReader;
    }
    public function processTransactions(string $inputFilePath): void
    {
        try {
            foreach ($this->csvReader->read($inputFilePath) as $transaction) {
                $commission = $this->transactionService->processTransaction($transaction);
                echo "{$commission}\n";
            }
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    }
}
