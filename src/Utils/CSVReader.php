<?php

// Enforces strict typing for the entire file.
declare(strict_types=1);

// Namespace for utility classes.
namespace App\Utils;

// Importing the necessary classes.
use App\Model\Transaction;
use DateTime;
use Exception;
use RuntimeException;

/**
 * Utility class to read CSV files and parse them into Transaction objects.
 */
class CSVReader
{
    /**
     * Tracks the current line being read in the CSV to report errors.
     * @var int
     */
    private int $currentLine = 1;

    /**
     * Reads a CSV file and converts each line to a Transaction object.
     *
     * @param string $filePath Path to the CSV file.
     * @throws Exception If the file cannot be read or there is an error parsing dates.
     * @return array An array of Transaction objects.
     */
    public function read(string $filePath): array
    {
        // Attempts to open the CSV file; throws an exception if it fails.
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Failed to open file: {$filePath}");
        }

        $transactions = [];

        // Reads each line of the CSV file.
        while (($data = fgetcsv($handle)) !== false) {
            // Checks for malformed data.
            if (count($data) !== 6) {
                echo "Warning: Malformed data on line {$this->currentLine}. "
                    . "Expected 6 columns, found " . count($data) . ".\n";
                $this->currentLine++;
                continue;
            }

            // Destructures the line into variables.
            [$date, $userId, $userType, $operationType, $amount, $currency] = $data;

            // Creates a new Transaction object.
            $transaction = new Transaction(
                (int)$userId,
                $userType,
                $operationType,
                (float)$amount,
                $currency,
                new DateTime($date)
            );

            // Adds the Transaction object to the array.
            $transactions[] = $transaction;
            $this->currentLine++;
        }

        // Closes the file handle.
        fclose($handle);

        // Returns the array of Transaction objects.
        return $transactions;
    }
}
