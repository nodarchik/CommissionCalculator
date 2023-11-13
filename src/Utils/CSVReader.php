<?php

declare(strict_types=1);

namespace App\Utils;

use App\Model\Transaction;
use DateTime;
use Exception;
use Generator;
use RuntimeException;

class CSVReader
{
    private int $currentLine = 1;

    /**
     * @throws Exception
     */
    public function read(string $filePath): Generator
    {
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException("Failed to open file: {$filePath}");
        }

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== 6) {
                echo "Warning: Malformed data on line {$this->currentLine}. "
                    . "Expected 6 columns, found " . count($data) . ".\n";
                $this->currentLine++;
                continue;
            }

            [$date, $userId, $userType, $operationType, $amount, $currency] = $data;

            yield new Transaction(
                $userId,
                $userType,
                $operationType,
                $amount,
                $currency,
                new DateTime($date)
            );
            $this->currentLine++;
        }
        fclose($handle);
    }
}
