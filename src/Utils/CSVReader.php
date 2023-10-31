<?php

declare(strict_types=1);

namespace App\Utils;

use App\Model\Transaction;

class CSVReader
{
    public function read(string $filePath): array
    {
        $handle = fopen($filePath, 'rb');
        $transactions = [];

        if ($handle !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $transactions[] = new Transaction(
                    $data[0],
                    (int)$data[1],
                    $data[2],
                    $data[3],
                    (float)$data[4],
                    $data[5]
                );
            }
            fclose($handle);
        }

        return $transactions;
    }
}
