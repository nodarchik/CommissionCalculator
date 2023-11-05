<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\TransactionService;
use App\Utils\CSVReader;
use App\Utils\CSVWriter;
use Exception;

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
        echo "Starting to process transactions from {$inputFilePath}...\n";

        try {
            $transactions = $this->csvReader->read($inputFilePath);
            $commissions = [];

            foreach ($transactions as $transaction) {
                $commission = $this->transactionService->processTransaction($transaction);
                $commissions[] = $commission;

                // Display transaction details and calculated commission in CLI
                echo "Processing transaction: Date: {$transaction->getDate()->format('Y-m-d')}, User ID: {$transaction->getUserId()}, "
                    . "User Type: {$transaction->getUserType()}, Operation: {$transaction->getOperationType()}, "
                    . "Amount: {$transaction->getAmount()}, Currency: {$transaction->getCurrency()}\n";
                echo "Calculated Commission: {$commission}\n";
            }

            $this->csvWriter->write($outputFilePath, $commissions);
            echo "Finished processing transactions. Results written to {$outputFilePath}\n";
        } catch (Exception $e) {
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    }
}
