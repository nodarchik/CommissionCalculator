<?php

declare(strict_types=1);

namespace App\Utils;

use App\Model\Transaction;
use DateTime;
use Exception;
use RuntimeException;

class CSVReader
{
    private int $currentLine = 1;

    /**
     * @throws Exception
     */
    public function read(string $filePath): array
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new RuntimeException("Failed to open file: {$filePath}");
        }

        $transactions = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== 6) {
                echo "Warning: Skipping malformed data on line {$this->currentLine}. Expected 6 columns, found " . count($data) . ".\n";
                $this->currentLine++;
                continue;
            }

            [$date, $userId, $userType, $operationType, $amount, $currency] = $data;

            $transaction = new Transaction(
                (int)$userId,
                $userType,
                $operationType,
                (float)$amount,
                $currency,
                new DateTime($date)
            );

            $transactions[] = $transaction;
            $this->currentLine++;
        }

        fclose($handle);

        return $transactions;
    }
}
