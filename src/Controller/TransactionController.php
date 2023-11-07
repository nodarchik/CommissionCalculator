<?php

// Enforces strict typing mode for type safety.
declare(strict_types=1);

// Defines the namespace for the controller class.
namespace App\Controller;

// Imports classes for services and utilities.
use App\Service\TransactionService;
use App\Utils\CSVReader;
use App\Utils\CSVWriter;
use Exception;

/**
 * Controller for handling transaction processes.
 */
class TransactionController
{
    /**
     * Service for transaction processing.
     * @var TransactionService
     */
    private TransactionService $transactionService;

    /**
     * Utility for reading CSV files.
     * @var CSVReader
     */
    private CSVReader $csvReader;

    /**
     * Utility for writing CSV files.
     * @var CSVWriter
     */
    private CSVWriter $csvWriter;

    /**
     * Initializes the controller with required services.
     *
     * @param TransactionService $transactionService Handles the business logic.
     * @param CSVReader          $csvReader          Reads the input CSV file.
     * @param CSVWriter          $csvWriter          Writes to the output CSV file.
     */
    public function __construct(
        TransactionService $transactionService,
        CSVReader $csvReader,
        CSVWriter $csvWriter
    ) {
        $this->transactionService = $transactionService;
        $this->csvReader = $csvReader;
        $this->csvWriter = $csvWriter;
    }

    /**
     * Processes transactions from input file and writes commissions to output.
     *
     * @param string $inputFilePath  Path to input CSV file with transactions.
     * @param string $outputFilePath Path to output CSV file for commissions.
     */
    public function processTransactions(string $inputFilePath, string $outputFilePath): void
    {
        echo "Starting process from {$inputFilePath}...\n";

        try {
            // Read transactions from the CSV file.
            $transactions = $this->csvReader->read($inputFilePath);
            $commissions = [];

            // Process each transaction and calculate commission.
            foreach ($transactions as $transaction) {
                $commission = $this->transactionService->processTransaction($transaction);
                $commissions[] = $commission;

                // Output transaction and commission details to CLI.
                echo "Transaction: Date: {$transaction->getDate()->format('Y-m-d')}, "
                    . "ID: {$transaction->getUserId()}, Type: {$transaction->getUserType()}, "
                    . "Operation: {$transaction->getOperationType()}, "
                    . "Amount: {$transaction->getAmount()}, "
                    . "Currency: {$transaction->getCurrency()}\n";
                echo "Commission: {$commission}\n";
            }

            // Write calculated commissions to output file.
            $this->csvWriter->write($outputFilePath, $commissions);
            echo "Processing complete. Results at {$outputFilePath}\n";
        } catch (Exception $e) {
            // Handle exceptions and log error messages.
            echo "An error occurred: " . $e->getMessage() . "\n";
        }
    }
}
